<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ai_usage_logs')) {
            Schema::create('ai_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('tokens_used');
                $table->string('feature'); // student_chat, teacher_quiz, admin_report
                if (Schema::hasTable('courses')) {
                    $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
                } else {
                    $table->unsignedBigInteger('course_id')->nullable();
                }
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
