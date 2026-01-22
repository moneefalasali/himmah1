<?php
// One-off test script to simulate lesson video upload without real Wasabi.
// Run: php test_upload.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

// A small Request subclass to bypass validation for this test (so we can focus on storage flow)
class TestRequest extends Request {
    public function validate(array $rules, ...$params)
    {
        return []; // bypass validation in this test environment
    }
}
use App\Http\Controllers\Teacher\TeacherLessonController;
use App\Models\Course;
use App\Models\Lesson;

// Ensure a course exists
$course = Course::first();
if (! $course) {
    $course = Course::create([
        'title' => 'Test Course for Upload',
        'description' => 'Auto-created for upload test',
        'price' => 0,
        'teacher_id' => 1,
    ]);
    echo "Created course id={$course->id}\n";
} else {
    echo "Using course id={$course->id}\n";
}

// Reconfigure 'wasabi' disk to point to local storage for test
config(['filesystems.disks.wasabi' => [
    'driver' => 'local',
    'root' => storage_path('app/test_wasabi'),
    'throw' => false,
]]);

if (! file_exists(storage_path('app/test_wasabi'))) {
    mkdir(storage_path('app/test_wasabi'), 0777, true);
}

// Create a small dummy file to act as video
$tmpDir = sys_get_temp_dir();
$tmpFile = $tmpDir . DIRECTORY_SEPARATOR . 'test_video.mp4';
file_put_contents($tmpFile, str_repeat('0', 1024 * 50)); // 50 KB dummy

$uploaded = new UploadedFile($tmpFile, 'test_video.mp4', 'video/mp4', null, true);

// Build request
$data = [
    'title' => 'Automated Test Lesson',
    'description' => 'Uploaded by test script',
    'duration' => 1,
];

$request = TestRequest::create('/fake', 'POST', $data);
// Attach file to request
$request->files->set('video', $uploaded);

// Call controller
$controller = new TeacherLessonController();
try {
    $response = $controller->store($request, $course);
    echo "Controller returned: ";
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        echo "JSON: " . $response->getContent() . "\n";
    } elseif ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "Redirect to: " . $response->getTargetUrl() . "\n";
    } else {
        var_dump($response);
    }

    $last = Lesson::where('course_id', $course->id)->latest()->first();
    if ($last) {
        echo "Created lesson id={$last->id}, video_path={$last->video_path}, video_url={$last->video_url}, processing_status={$last->processing_status}\n";
    } else {
        echo "No lesson was created.\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

// Cleanup temp file
@unlink($tmpFile);

echo "Done.\n";
