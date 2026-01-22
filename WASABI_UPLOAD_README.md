Summary of Wasabi upload audit and implemented changes

Problem
- The server experienced PostTooLargeException when final lesson form submitted the entire local file (browser sent full file in final POST), exceeding PHP `post_max_size`.
- Existing chunk assembly used file reads that could load parts into memory in suboptimal ways.

Implemented solution (concise)
- Added direct browser-to-Wasabi multipart upload support via presigned URLs:
  - `POST /teacher/video/presign/initiate` (returns `uploadId`, `key`, and presigned part URLs)
  - `POST /teacher/video/presign/complete` (accepts `uploadId`, `key`, and `parts` array to complete)
- Implemented `App\Http\Controllers\Teacher\VideoPresignController` using AWS SDK S3Client configured with Wasabi env settings.
- Kept existing server-side chunk handling as fallback and improved `App\Services\VideoService` to stream-copy chunks and to `put()` using a stream (avoids loading full files into memory).
- Added logging for presign/multipart errors and upload errors.

Why this approach
- Direct multipart uploads from the browser avoid routing large file bytes through the application server, eliminating PostTooLarge and memory concerns and minimizing server bandwidth.
- Multipart upload supports very large files (GBs) and resume/retry per part.

Files changed/added
- Added: `app/Http/Controllers/Teacher/VideoPresignController.php`
- Modified: `app/Services/VideoService.php` (stream copy for parts, safer uploadRawToWasabi with logging)
- Modified: `routes/web.php` (added presign initiate/complete routes for teacher and admin)
- Added this `WASABI_UPLOAD_README.md`

Required front-end changes (next steps)
- Update teacher/admin upload UI to:
  1. Compute number of parts (e.g., part size 10MB) and call `/teacher/video/presign/initiate` with `filename` and `parts`.
  2. Upload each part directly to the returned presigned `url` using `PUT` requests; capture each response `ETag`.
  3. When all parts uploaded, call `/teacher/video/presign/complete` with `uploadId`, `key`, and `parts` array ordering by `PartNumber` with their `ETag`s.
  4. On success, persist the returned `key` (object path) as the lesson video path in the DB (existing lesson creation flow should accept a `video_path` field).

PHP configuration notice (optional)
- With presigned direct uploads, server `post_max_size` / `upload_max_filesize` are less critical; however if chunked uploads are used via server fallback, set sensible values in `php.ini` (example):

```
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
```

Testing
- Use the browser upload UI to test direct uploads; watch network requests for PUT to Wasabi presigned URLs (domain will be Wasabi endpoint) and ensure `/teacher/video/presign/complete` returns success.
- Check `storage/logs/laravel.log` for any presign or upload errors.

Security
- Presign endpoints should be protected via `auth` middleware (already added).
- Do not expose credentials in clients.

Wasabi bucket CORS requirement
- The bucket must allow cross-origin PUT requests and expose the `ETag` header so the browser can read the part ETag values. Example minimal CORS configuration for Wasabi (JSON):

```
[
  {
    "AllowedHeaders": ["*"],
    "AllowedMethods": ["PUT", "POST", "GET", "HEAD"],
    "AllowedOrigins": ["*"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3000
  }
]
```

Adjust `AllowedOrigins` for production to restrict domains.

If you want, I can now:
- Implement the front-end JS changes in `resources/views/teacher/courses/lessons.blade.php` to call the new endpoints and perform multipart uploads with progress (recommended), or
- Implement a simpler presigned single PUT workflow for smaller files.

Choose which front-end approach you prefer and I will implement it for both teacher and admin UIs.

Quick mitigation for PostTooLarge exceptions
- I removed the `name` attribute from the `<input type="file">` in the teacher lesson form so browsers will not include the full file in the final form POST if JavaScript fails or the teacher submits early. The uploader now requires JS to perform direct multipart uploads or server-side chunked uploads; this is intentional to avoid PHP `post_max_size` errors.

If you prefer to allow full-file POSTs (not recommended for large files), increase `post_max_size` and `upload_max_filesize` in your `php.ini` (XAMPP: `c:\xampp\php\php.ini`) and restart Apache. Example safe values for large uploads:

```ini
upload_max_filesize = 2048M
post_max_size = 2050M
max_execution_time = 600
```

Restart Apache after editing `php.ini` (use XAMPP Control Panel or services.msc).
