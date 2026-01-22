<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class UpdateCoursePricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            // Determine course size based on total lessons or duration
            $courseSize = 'normal';
            
            if ($course->total_lessons > 20 || ($course->duration && $course->duration > 600)) {
                $courseSize = 'large';
            }
            
            // Set pricing based on course size
            $price = $courseSize === 'large' ? rand(149, 179) : rand(129, 149);
            
            $course->update([
                'course_size' => $courseSize,
                'includes_summary' => true,
                'includes_tajmeeat' => true,
                'price' => $price,
            ]);
            
            $this->command->info("Updated course '{$course->title}': Size={$courseSize}, Price={$price} SAR");
        }
    }
}

