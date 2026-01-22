<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes([
            'middleware' => [\App\Http\Middleware\LogBroadcastAuth::class, 'auth'],
        ]);

        require base_path('routes/channels.php');
    }
}
