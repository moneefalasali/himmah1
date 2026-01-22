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
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('courses', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete()->after('category_id');
            }
            if (!Schema::hasColumn('courses', 'university_id')) {
                $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete()->after('subject_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'university_id')) {
                $table->dropConstrainedForeignId('university_id');
            }
            if (Schema::hasColumn('courses', 'subject_id')) {
                $table->dropConstrainedForeignId('subject_id');
            }
            if (Schema::hasColumn('courses', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });
    }
};
