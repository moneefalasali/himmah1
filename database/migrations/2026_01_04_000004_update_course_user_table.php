<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // إذا كان الجدول غير موجود نقوم بإنشائه، وإذا كان موجوداً نقوم بتحديثه
        if (!Schema::hasTable('course_user')) {
            Schema::create('course_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->timestamp('subscription_start')->nullable();
                $table->timestamp('subscription_end')->nullable();
                $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
                $table->timestamps();
            });
        } else {
            Schema::table('course_user', function (Blueprint $table) {
                if (!Schema::hasColumn('course_user', 'subscription_start')) {
                    $table->timestamp('subscription_start')->nullable();
                }
                if (!Schema::hasColumn('course_user', 'subscription_end')) {
                    $table->timestamp('subscription_end')->nullable();
                }
                if (!Schema::hasColumn('course_user', 'status')) {
                    $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropColumn(['subscription_start', 'subscription_end', 'status']);
        });
    }
};
