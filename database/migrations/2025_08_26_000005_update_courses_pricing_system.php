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
            // Add course size field to determine pricing
            $table->enum('course_size', ['normal', 'large'])->default('normal')->after('total_lessons');
            
            // Add fields for summary and tajmeeat inclusion
            $table->boolean('includes_summary')->default(true)->after('course_size');
            $table->boolean('includes_tajmeeat')->default(true)->after('includes_summary');
            
            // Update default price to reflect new pricing structure
            $table->decimal('price', 8, 2)->default(139.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['course_size', 'includes_summary', 'includes_tajmeeat']);
            $table->decimal('price', 8, 2)->default(300.00)->change();
        });
    }
};

