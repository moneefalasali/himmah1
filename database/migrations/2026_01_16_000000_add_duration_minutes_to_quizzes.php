<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('quizzes')) {
            if (!Schema::hasColumn('quizzes', 'duration_minutes')) {
                Schema::table('quizzes', function (Blueprint $table) {
                    $table->integer('duration_minutes')->nullable()->after('description');
                });

                // If there is an older `time_limit` column, copy its values
                if (Schema::hasColumn('quizzes', 'time_limit')) {
                    DB::table('quizzes')->whereNotNull('time_limit')->update([
                        'duration_minutes' => DB::raw('time_limit')
                    ]);
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('quizzes') && Schema::hasColumn('quizzes', 'duration_minutes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropColumn('duration_minutes');
            });
        }
    }
};
