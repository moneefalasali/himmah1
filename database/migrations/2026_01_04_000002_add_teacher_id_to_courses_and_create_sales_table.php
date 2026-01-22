<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // إضافة user_id لجدول الدورات لربطها بالمعلم
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                }
                if (!Schema::hasColumn('courses', 'status')) {
                    $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                }
            });
        }

        // إنشاء جدول المبيعات لتتبع الأرباح والعمولات
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // الطالب المشتري
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // المعلم صاحب الدورة
                $table->decimal('amount', 10, 2); // سعر البيع الإجمالي
                $table->decimal('teacher_commission', 10, 2); // حصة المعلم (40%)
                $table->decimal('admin_commission', 10, 2); // حصة المنصة (60%)
                $table->string('transaction_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sales');
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn(['user_id', 'status']);
            });
        }
    }
};
