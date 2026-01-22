<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            // Add 'instructor' and 'teacher' to enum values for role
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('user','admin','instructor','teacher') NOT NULL DEFAULT 'user'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('user','admin') NOT NULL DEFAULT 'user'");
        }
    }
};
