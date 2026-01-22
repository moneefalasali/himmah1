<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CourseSystemTest extends TestCase
{
    public function test_guest_cannot_access_student_dashboard()
    {
        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }
    public function test_student_can_view_active_courses()
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->actingAs($user);
        $response = $this->get('/courses');
        $response->assertStatus(200);
    }
}
