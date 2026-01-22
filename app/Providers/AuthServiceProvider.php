<?php
namespace App\Providers;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\ChatRoom;
use App\Policies\CoursePolicy;
use App\Policies\QuizPolicy;
use App\Policies\ChatPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
        Quiz::class => QuizPolicy::class,
        ChatRoom::class => ChatPolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
