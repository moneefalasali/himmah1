<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\ChatRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class DeepSystemTest extends TestCase
{
    // 1. اختبار نظام الكويزات (Edge Cases)
    public function test_quiz_submission_with_empty_answers()
    {
        $user = User::factory()->create(['role' => 'student']);
        $quiz = Quiz::factory()->create(['status' => 'published']);
        $this->actingAs($user);
        $response = $this->post(route('student.quizzes.submit', $quiz->id), [
            'answers' => []
        ]);
        $response->assertStatus(302); // يجب أن يعيد التوجيه مع خطأ أو نتيجة صفرية
    }

    // 2. اختبار نظام الاشتراكات (الوصول للمحتوى)
    public function test_student_cannot_access_unsubscribed_course_lessons()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['status' => 'active']);
        $this->actingAs($user);
        $response = $this->get(route('chat.show', ['room' => 1])); // محاولة دخول دردشة كورس غير مشترك فيه
        $response->assertStatus(403);
    }

    // 3. اختبار نظام الدردشة (الخصوصية)
    public function test_unauthorized_user_cannot_access_service_chat()
    {
        $user1 = User::factory()->create(['role' => 'student']);
        $user2 = User::factory()->create(['role' => 'student']);
        $room = ChatRoom::create([
            'user_id' => $user1->id,
            'type' => 'service',
            'name' => 'Support'
        ]);
        $this->actingAs($user2);
        $response = $this->get(route('chat.show', $room->id));
        $response->assertStatus(403);
    }
}
