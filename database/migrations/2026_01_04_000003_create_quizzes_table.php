<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->nullable(); // وقت محدد بالدقائق
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('show_results_immediately')->default(true);
            $table->timestamps();
            });
        }
        if (!Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer']);
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
            });
        }
        if (!Schema::hasTable('options')) {
            Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            });
        }
        if (!Schema::hasTable('quiz_results')) {
            Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الطالب
            $table->integer('total_points');
            $table->integer('earned_points');
            $table->decimal('percentage', 5, 2);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            });
        }
        if (!Schema::hasTable('quiz_answers')) {
            Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('answer_text')->nullable(); // للإجابات القصيرة
            $table->foreignId('option_id')->nullable()->constrained()->onDelete('cascade'); // للاختيارات
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_results');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
