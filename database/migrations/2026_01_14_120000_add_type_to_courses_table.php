<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('courses', 'type')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->enum('type', ['recorded', 'online'])->nullable()->default('recorded')->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'type')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
