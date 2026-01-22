<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $teacherId = $teacher ? $teacher->id : 1;

        $courses = [
            [
                'title' => 'دورة البرمجة بلغة PHP',
                'description' => 'تعلم أساسيات البرمجة بلغة PHP من الصفر حتى الاحتراف.',
                'price' => 199.00,
                'instructor_name' => 'د. أحمد سعيد',
                'user_id' => $teacherId,
                'status' => 'active',
                'total_lessons' => 15,
                'duration' => 900,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'description' => 'دورة متقدمة في إطار العمل Laravel لبناء تطبيقات ويب قوية وقابلة للتوسع.',
                'price' => 399.00,
                'instructor_name' => 'د. أحمد سعيد',
                'user_id' => $teacherId,
                'status' => 'active',
                'total_lessons' => 20,
                'duration' => 1200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $courseData) {
            $courseId = DB::table('courses')->insertGetId($courseData);
            $this->addLessonsForCourse($courseId, $courseData['total_lessons'], $courseData['title']);
        }
    }

    private function addLessonsForCourse($courseId, $totalLessons, $courseTitle)
    {
        for ($i = 1; $i <= $totalLessons; $i++) {
            DB::table('lessons')->insert([
                'course_id' => $courseId,
                'title' => "الدرس {$i} في {$courseTitle}",
                'description' => "وصف مفصل للدرس رقم {$i}",
                'video_url' => "https://example.com/video{$i}.mp4",
                'duration' => rand(30, 90),
                'order' => $i,
                'is_free' => $i <= 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
