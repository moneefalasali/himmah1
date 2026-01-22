<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lessons', function (Blueprint $col) {
            $col->string('video_path')->nullable()->after('video_url');
            $col->string('hls_path')->nullable()->after('video_path');
            $col->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('hls_path');
            $col->string('processing_error')->nullable()->after('processing_status');
            $col->json('video_metadata')->nullable()->after('processing_error');
        });

        Schema::create('video_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('action'); // e.g., 'play', 'pause', 'seek', 'complete'
            $table->integer('timestamp_in_video')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('lessons', function (Blueprint $col) {
            $col->dropColumn(['video_path', 'hls_path', 'processing_status', 'processing_error', 'video_metadata']);
        });
        Schema::dropIfExists('video_audit_logs');
    }
};
