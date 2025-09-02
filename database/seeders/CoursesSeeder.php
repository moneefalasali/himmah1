<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'أساسيات البرمجة بـ PHP',
                'description' => 'دورة شاملة لتعلم أساسيات البرمجة باستخدام لغة PHP من الصفر حتى الاحتراف. تشمل الدورة المفاهيم الأساسية، البرمجة الكائنية، والتطبيقات العملية.',
                'price' => 299.00,
                'instructor_name' => 'أحمد محمد',
                'status' => 'active',
                'total_lessons' => 15,
                'duration' => 900, // 15 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تطوير تطبيقات الويب بـ Laravel',
                'description' => 'تعلم كيفية بناء تطبيقات ويب متقدمة باستخدام إطار العمل Laravel. تشمل الدورة قواعد البيانات، المصادقة، وأفضل الممارسات.',
                'price' => 399.00,
                'instructor_name' => 'فاطمة علي',
                'status' => 'active',
                'total_lessons' => 20,
                'duration' => 1200, // 20 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تصميم قواعد البيانات',
                'description' => 'دورة متخصصة في تصميم وإدارة قواعد البيانات باستخدام MySQL. تشمل النمذجة، الاستعلامات المتقدمة، والأمان.',
                'price' => 249.00,
                'instructor_name' => 'محمد سالم',
                'status' => 'active',
                'total_lessons' => 12,
                'duration' => 720, // 12 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'أساسيات الأمن السيبراني',
                'description' => 'تعلم أساسيات الأمن السيبراني وحماية التطبيقات والبيانات من التهديدات الأمنية المختلفة.',
                'price' => 349.00,
                'instructor_name' => 'سارة أحمد',
                'status' => 'active',
                'total_lessons' => 18,
                'duration' => 1080, // 18 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تطوير واجهات المستخدم بـ React',
                'description' => 'دورة شاملة لتعلم تطوير واجهات المستخدم التفاعلية باستخدام مكتبة React وأحدث التقنيات.',
                'price' => 449.00,
                'instructor_name' => 'عبدالله خالد',
                'status' => 'active',
                'total_lessons' => 16,
                'duration' => 960, // 16 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تعلم JavaScript من الصفر',
                'description' => 'دورة شاملة لتعلم JavaScript من الأساسيات حتى المستوى المتقدم مع تطبيقات عملية.',
                'price' => 199.00,
                'instructor_name' => 'نورا محمد',
                'status' => 'active',
                'total_lessons' => 14,
                'duration' => 840, // 14 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تطوير تطبيقات الموبايل بـ Flutter',
                'description' => 'تعلم تطوير تطبيقات الموبايل للـ iOS و Android باستخدام Flutter من Google.',
                'price' => 499.00,
                'instructor_name' => 'خالد عبدالرحمن',
                'status' => 'active',
                'total_lessons' => 22,
                'duration' => 1320, // 22 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تعلم Python للمبتدئين',
                'description' => 'دورة مخصصة للمبتدئين لتعلم لغة Python مع تطبيقات عملية في تحليل البيانات.',
                'price' => 179.00,
                'instructor_name' => 'ريم أحمد',
                'status' => 'active',
                'total_lessons' => 13,
                'duration' => 780, // 13 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تصميم المواقع بـ HTML و CSS',
                'description' => 'دورة أساسية لتعلم تصميم المواقع باستخدام HTML و CSS مع أفضل الممارسات.',
                'price' => 149.00,
                'instructor_name' => 'علي حسن',
                'status' => 'active',
                'total_lessons' => 10,
                'duration' => 600, // 10 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'إدارة المشاريع البرمجية',
                'description' => 'تعلم كيفية إدارة المشاريع البرمجية باستخدام منهجيات Agile و Scrum.',
                'price' => 299.00,
                'instructor_name' => 'د. أحمد سعيد',
                'status' => 'active',
                'total_lessons' => 11,
                'duration' => 660, // 11 hours
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $courseData) {
            $courseId = DB::table('courses')->insertGetId($courseData);
            
            // إضافة دروس لكل دورة
            $this->addLessonsForCourse($courseId, $courseData['total_lessons'], $courseData['title']);
        }
    }

    private function addLessonsForCourse($courseId, $totalLessons, $courseTitle)
    {
        $lessonTitles = $this->getLessonTitles($courseTitle, $totalLessons);
        
        for ($i = 1; $i <= $totalLessons; $i++) {
            $title = $lessonTitles[$i - 1] ?? "الدرس {$i}";
            
            DB::table('lessons')->insert([
                'course_id' => $courseId,
                'title' => $title,
                'description' => "وصف مفصل للدرس: {$title}",
                'video_url' => "https://example.com/video{$i}.mp4",
                'duration' => rand(30, 90), // 30-90 minutes
                'order' => $i,
                'is_free' => $i <= 3, // أول 3 دروس مجانية
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getLessonTitles($courseTitle, $totalLessons)
    {
        $titles = [];
        
        if (str_contains($courseTitle, 'PHP')) {
            $titles = [
                'مقدمة في PHP',
                'تثبيت وإعداد البيئة',
                'المتغيرات والأنواع',
                'الجمل الشرطية',
                'الحلقات التكرارية',
                'الدوال',
                'المصفوفات',
                'معالجة النماذج',
                'قواعد البيانات',
                'الجلسات والكوكيز',
                'البرمجة الكائنية',
                'معالجة الأخطاء',
                'الأمان في PHP',
                'تحسين الأداء',
                'مشروع تطبيقي نهائي'
            ];
        } elseif (str_contains($courseTitle, 'Laravel')) {
            $titles = [
                'مقدمة في Laravel',
                'تثبيت وإعداد المشروع',
                'Routing الأساسي',
                'Controllers',
                'Views و Blade',
                'قواعد البيانات و Eloquent',
                'Migrations',
                'Authentication',
                'Authorization',
                'Middleware',
                'Validation',
                'File Uploads',
                'API Development',
                'Testing',
                'Deployment',
                'Caching',
                'Queue Jobs',
                'Events و Listeners',
                'Packages',
                'مشروع تطبيقي نهائي'
            ];
        } elseif (str_contains($courseTitle, 'قواعد البيانات')) {
            $titles = [
                'مقدمة في قواعد البيانات',
                'تصميم قاعدة البيانات',
                'أنواع البيانات',
                'الاستعلامات الأساسية',
                'JOIN Operations',
                'Subqueries',
                'Indexes',
                'Stored Procedures',
                'Triggers',
                'Normalization',
                'Backup و Recovery',
                'الأمان في قواعد البيانات'
            ];
        } elseif (str_contains($courseTitle, 'React')) {
            $titles = [
                'مقدمة في React',
                'تثبيت وإعداد المشروع',
                'Components الأساسية',
                'Props و State',
                'Event Handling',
                'Conditional Rendering',
                'Lists و Keys',
                'Forms',
                'Lifecycle Methods',
                'Hooks',
                'Context API',
                'Routing',
                'State Management',
                'Testing',
                'Performance Optimization',
                'Deployment'
            ];
        } else {
            // عناوين عامة للدورات الأخرى
            for ($i = 1; $i <= $totalLessons; $i++) {
                $titles[] = "الدرس {$i}";
            }
        }
        
        return array_slice($titles, 0, $totalLessons);
    }
}

