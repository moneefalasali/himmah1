<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user-enrollment {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check purchases and course_user pivot for a given user email';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $this->info("USER_ID: {$user->id}");

        $completed = $user->purchases()->where('payment_status', 'completed')->with('course')->get();
        $this->info('COMPLETED_PURCHASES_COUNT: ' . $completed->count());
        foreach ($completed as $p) {
            $this->line("PURCHASE id: {$p->id} course_id: {$p->course_id} amount: {$p->amount}");
        }

        $enrolled = $user->enrolledCourses()->get();
        $this->info('PIVOT_ENROLLED_COUNT: ' . $enrolled->count());
        foreach ($enrolled as $c) {
            $status = $c->pivot->status ?? 'n/a';
            $this->line("PIVOT course_id: {$c->id} title: {$c->title} pivot_status: {$status}");
        }

        return 0;
    }
}
