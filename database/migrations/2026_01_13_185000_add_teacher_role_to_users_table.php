<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modify enum to include 'teacher'
        if (DB::getDriverName() === 'mysql') {
            // Ensure any unexpected role values are normalized before changing the enum
            DB::statement("UPDATE `users` SET `role` = 'user' WHERE `role` NOT IN ('user','admin','teacher') OR `role` IS NULL");
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('user','admin','teacher') NOT NULL DEFAULT 'user'");
        } else {
            // For other DBs, fallback: add string column and migrate values
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_tmp')->default('user')->after('password');
            });
            DB::table('users')->update(['role_tmp' => DB::raw("COALESCE(role, 'user')")]);
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->after('password');
            });
            DB::table('users')->update(['role' => DB::raw('role_tmp')]);
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_tmp');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('user','admin') NOT NULL DEFAULT 'user'");
        } else {
            // No-op for other drivers in down migration
        }
    }
};
