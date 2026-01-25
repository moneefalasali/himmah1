<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Use raw statement to avoid requiring doctrine/dbal on production.
     *
     * @return void
     */
    public function up()
    {
        // Make video_url nullable to prevent integrity constraint errors
        // Use DB statement to alter column type safely across MySQL versions.
        DB::statement("ALTER TABLE `lessons` MODIFY `video_url` VARCHAR(191) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to NOT NULL with empty default to avoid data loss
        DB::statement("ALTER TABLE `lessons` MODIFY `video_url` VARCHAR(191) NOT NULL DEFAULT ''");
    }
};
