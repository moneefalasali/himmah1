<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('live_sessions')) {
            Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            // create the column now; add FK constraint later if the referenced table exists
            $table->unsignedBigInteger('course_id');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('topic');
            $table->string('meeting_id')->unique();
            $table->text('start_url');
            $table->text('join_url');
            $table->dateTime('start_time');
            $table->integer('duration'); // بالدقائق
            $table->string('status')->default('active'); // active, cancelled, finished
            $table->string('recording_path')->nullable();
            $table->string('hls_path')->nullable();
            $table->timestamps();
            });
            // Add foreign key constraint for course_id only if the courses table exists
            if (Schema::hasTable('courses')) {
                Schema::table('live_sessions', function (Blueprint $table) {
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                });
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('live_sessions');
    }
};
