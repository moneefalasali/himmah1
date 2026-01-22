<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureSingleSession::class])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Video Routes
    Route::get('/lessons/{lesson}/stream-url', [\App\Http\Controllers\VideoStreamController::class, 'getStreamUrl']);
    Route::post('/video/log', [\App\Http\Controllers\VideoLogController::class, 'log']);
    Route::post('/admin/video/upload', [\App\Http\Controllers\Admin\VideoUploadController::class, 'upload']);
});
