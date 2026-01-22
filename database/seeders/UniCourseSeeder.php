<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\University;
use App\Models\Course;
use App\Models\UniCourse;
use App\Models\Lesson;
use App\Models\CourseLessonMapping;

class UniCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = University::all();
        $courses = Course::where('status', 'active')->get();

        if ($universities->isEmpty() || $courses->isEmpty()) {
            $this->command->info('No universities or courses found. Please run UniversitySeeder and create some courses first.');
            return;
        }

        // Map all courses to all universities by default
        foreach ($universities as $university) {
            foreach ($courses as $course) {
                // Create uni course
                $uniCourse = UniCourse::create([
                    'university_id' => $university->id,
                    'course_id' => $course->id,
                    'custom_name' => null, // Use original course name
                ]);

                // Map all lessons from the original course with their original order
                $lessons = Lesson::where('course_id', $course->id)
                    ->orderBy('order')
                    ->get();

                foreach ($lessons as $lesson) {
                    CourseLessonMapping::create([
                        'uni_course_id' => $uniCourse->id,
                        'lesson_id' => $lesson->id,
                        'order' => $lesson->order,
                    ]);
                }

                $this->command->info("Mapped course '{$course->title}' to university '{$university->name}' with {$lessons->count()} lessons");
            }
        }

        // Create some custom course names for specific universities
        $this->createCustomCourseNames();
    }

    /**
     * Create custom course names for specific universities
     */
    private function createCustomCourseNames()
    {
        $customMappings = [
            'جامعة الملك سعود' => [
                // Example: if there's a programming course, give it a custom name
                'البرمجة' => 'مقدمة في علوم الحاسب - CS101',
                'تطوير المواقع' => 'تطوير تطبيقات الويب - IT230',
                'قواعد البيانات' => 'أنظمة قواعد البيانات - CS340',
            ],
            'جامعة الملك عبدالعزيز' => [
                'البرمجة' => 'أساسيات البرمجة - CPCS202',
                'تطوير المواقع' => 'برمجة الإنترنت - CPCS403',
                'الذكاء الاصطناعي' => 'مقدمة في الذكاء الاصطناعي - CPCS481',
            ],
            'جامعة الملك فهد للبترول والمعادن' => [
                'البرمجة' => 'Programming Fundamentals - ICS103',
                'تطوير المواقع' => 'Web Development - ICS324',
                'هندسة البرمجيات' => 'Software Engineering - SWE316',
            ],
        ];

        foreach ($customMappings as $universityName => $courseMappings) {
            $university = University::where('name', $universityName)->first();
            
            if (!$university) {
                continue;
            }

            foreach ($courseMappings as $courseKeyword => $customName) {
                $course = Course::where('title', 'like', "%{$courseKeyword}%")->first();
                
                if ($course) {
                    $uniCourse = UniCourse::where('university_id', $university->id)
                        ->where('course_id', $course->id)
                        ->first();
                    
                    if ($uniCourse) {
                        $uniCourse->update(['custom_name' => $customName]);
                        $this->command->info("Updated course name for '{$course->title}' at '{$university->name}' to '{$customName}'");
                    }
                }
            }
        }
    }
}

