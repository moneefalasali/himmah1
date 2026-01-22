<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\User;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 0, 500),
            'instructor_name' => $this->faker->name(),
            'image' => null,
            'status' => 'active',
            'total_lessons' => 0,
            'duration' => null,
        ];
    }
}
