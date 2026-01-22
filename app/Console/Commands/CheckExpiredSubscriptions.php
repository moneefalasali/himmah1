<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'فحص الاشتراكات المنتهية وتحديث حالتها تلقائياً';

    public function handle()
    {
        $affected = DB::table('course_user')
            ->where('status', 'active')
            ->where('subscription_end', '<', now())
            ->update(['status' => 'expired', 'updated_at' => now()]);

        $this->info("تم تحديث {$affected} اشتراك منتهي.");
    }
}
