<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AuthSystemTest extends TestCase
{
    public function test_user_can_view_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($user);
    }
}
