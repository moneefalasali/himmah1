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
        Schema::table("lessons", function (Blueprint $table) {
            $table->string("vimeo_video_id")->nullable()->after("video_url");
            $table->string("video_platform")->nullable()->after("vimeo_video_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("lessons", function (Blueprint $table) {
            $table->dropColumn("vimeo_video_id");
            $table->dropColumn("video_platform");
        });
    }
};
