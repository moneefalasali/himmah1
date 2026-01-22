<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Course;
use App\Models\Quiz;

class TeacherQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_question_with_options_and_student_can_take_quiz()
    {
        // create teacher
        $teacher = User::factory()->create(['role' => 'teacher', 'is_instructor' => 1]);
        // create student
        $student = User::factory()->create(['role' => 'user']);

        // teacher creates a course and a quiz
        $this->actingAs($teacher);
        $course = Course::factory()->create(['user_id' => $teacher->id]);
        $quiz = Quiz::create([ 'course_id' => $course->id, 'title' => 'Test Quiz', 'is_active' => true ]);

        // teacher adds question with options via POST
        $resp = $this->post('/teacher/quizzes/'.$quiz->id.'/questions', [
            'question_text' => 'What is 2+2?',
            'type' => 'multiple_choice',
            'points' => 1,
            'options' => [
                ['option_text' => '3', 'is_correct' => 0],
                ['option_text' => '4', 'is_correct' => 1],
            ],
        ]);

        $resp->assertRedirect(route('teacher.quizzes.show', $quiz));

        // student takes the quiz
        $this->actingAs($student);
        $get = $this->get(route('student.quizzes.take', $quiz));
        if ($get->status() !== 200) {
            // dump response for debugging
            echo $get->getContent();
        }
        $get->assertStatus(200);

        // submit answers (choose the correct option)
        $question = $quiz->questions()->first();
        $correct = $question->options()->where('is_correct', true)->first();

        $submit = $this->post(route('student.quizzes.submit', $quiz), [
            'answers' => [ $question->id => $correct->id ]
        ]);

        $submit->assertRedirect();
        // check result exists in DB
        $this->assertDatabaseHas('quiz_results', ['quiz_id' => $quiz->id, 'user_id' => $student->id]);
    }
}
