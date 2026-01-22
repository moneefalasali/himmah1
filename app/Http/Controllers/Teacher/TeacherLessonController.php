<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Jobs\ProcessVideoHLS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherLessonController extends Controller
{
    public function index(Course $course)
    {
        // Load sections with their lessons if sections are used
        $sections = $course->sections()->with('lessons')->get();
        $lessonsWithoutSection = $course->lessons()->whereNull('section_id')->get();

        return view('teacher.courses.lessons', compact('course', 'sections', 'lessonsWithoutSection'));
    }
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:2097152', // up to 2GB (in KB)
            'video_path' => 'nullable|string',
            'video_url' => 'nullable|url',
            'vimeo_video_id' => 'nullable|string',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'section_id' => 'nullable|exists:sections,id',
            'is_free' => 'nullable|boolean',
        ]);

        // Determine source: external URL / Vimeo ID / already-uploaded path / file upload
        $path = null;
        $url = '';

        $videoUrlInput = trim((string) $request->input('video_url', ''));
        $vimeoId = trim((string) $request->input('vimeo_video_id', ''));
        $videoPathInput = $request->input('video_path');

        if ($videoUrlInput !== '') {
            // External URL provided by teacher (YouTube/Vimeo/other embed)
            $url = $videoUrlInput;
            if (stripos($url, 'vimeo.com') !== false) {
                $platform = 'vimeo';
            } elseif (stripos($url, 'youtube.com') !== false || stripos($url, 'youtu.be') !== false) {
                $platform = 'youtube';
            } else {
                $platform = 'external';
            }
        } elseif ($vimeoId !== '') {
            $url = 'https://vimeo.com/' . $vimeoId;
            $platform = 'vimeo';
        } else {
            $platform = null;
            // Normalize video_path input: treat literal 'null' and empty strings as absent
            if ($videoPathInput !== null && $videoPathInput !== '' && $videoPathInput !== 'null') {
                $path = $videoPathInput;
                // attempt to create a temporary/public URL if possible
                try {
                    $url = Storage::disk('wasabi')->url($path) ?: '';
                } catch (\Exception $e) {
                    $url = '';
                }
            } elseif ($request->hasFile('video')) {
                $file = $request->file('video');
                // Use the configured Wasabi disk
                $path = Storage::disk('wasabi')->putFile('raw_videos', $file, 'public');
                // try to get public url
                try {
                    $url = Storage::disk('wasabi')->url($path) ?: '';
                } catch (\Exception $e) {
                    $url = '';
                }
                $platform = $path ? 'wasabi' : null;
            }
        }

        // calculate order
        $maxOrder = $course->lessons()->max('order') ?? 0;

        // 2. إنشاء سجل الدرس مع الحقول المتوافقة مع قاعدة البيانات
        $lessonData = [
            'title' => $request->title,
            'description' => $request->description ?? null,
            'video_path' => $path,
            'video_url' => $url ?? '',
            'video_platform' => $platform ?? ($path ? 'wasabi' : null),
            'duration' => $request->duration ?? null,
            'section_id' => $request->section_id ?: null,
            'is_free' => $request->has('is_free') ? (bool)$request->is_free : false,
            'order' => $maxOrder + 1,
        ];

        // If we have a Wasabi path, mark as processing so HLS job runs. If external URL/Vimeo, mark completed.
        if ($path) {
            $lessonData['processing_status'] = 'processing';
        } elseif (!empty($url)) {
            $lessonData['processing_status'] = 'completed';
        } else {
            $lessonData['processing_status'] = 'pending';
        }

        $lesson = $course->lessons()->create($lessonData);

        // 3. إرسال مهمة معالجة HLS للخلفية إن وُجدت مسار الفيديو
        if ($path) {
            ProcessVideoHLS::dispatch($lesson);
        }

        // إذا كان الطلب عبر AJAX (XHR) أعد رابط إعادة التوجيه كـ JSON لكي يتعامل السكربت في الواجهة
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'redirect' => route('teacher.courses.lessons.index', $course),
            ]);
        }

        return back()->with('success', 'تم رفع الفيديو بنجاح، جاري المعالجة...');
    }

    /**
     * Attach an already-uploaded Wasabi key to an existing lesson and dispatch processing.
     */
    public function attachVideo(Request $request, Course $course, Lesson $lesson)
    {
        $request->validate([
            'video_path' => 'required|string',
        ]);

        // ensure lesson belongs to this course
        if ($lesson->course_id !== $course->id) {
            abort(403);
        }

        // ensure current user owns the course (teacher)
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $path = $request->input('video_path');
        $lesson->video_path = $path;
        try {
            $lesson->video_url = Storage::disk('wasabi')->url($path) ?: '';
        } catch (\Exception $e) {
            $lesson->video_url = '';
        }
        $lesson->video_platform = 'wasabi';
        $lesson->processing_status = 'processing';
        $lesson->processing_error = null;
        $lesson->save();

        ProcessVideoHLS::dispatch($lesson);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Video attached and processing started']);
        }

        return back()->with('success', 'تم إرفاق مسار الفيديو وبدأت معالجة HLS.');
    }
}
