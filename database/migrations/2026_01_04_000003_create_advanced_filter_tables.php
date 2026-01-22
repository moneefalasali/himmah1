<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // جدول المراحل التعليمية (Categories)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ابتدائي، متوسط، ثانوي، إلخ
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // جدول المقررات / التخصصات (Subjects)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // رياضيات، فيزياء، IELTS، إلخ
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // تحديث جدول الدورات (Courses) لإضافة العلاقات الجديدة ونوع الدورة
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('courses', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('courses', 'type')) {
                $table->enum('type', ['recorded', 'online'])->default('recorded');
            }
            if (!Schema::hasColumn('courses', 'is_published')) {
                $table->boolean('is_published')->default(true);
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['category_id', 'subject_id', 'type', 'is_published']);
        });
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('categories');
    }
};
