<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_message_deletions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('deleted_for_everyone')->default(false);
            $table->timestamps();
            $table->unique(['chat_message_id','user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_message_deletions');
    }
};
