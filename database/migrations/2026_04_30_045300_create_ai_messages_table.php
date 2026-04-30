<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->enum('role', ['user', 'assistant', 'system', 'tool']);
            $table->longText('content');
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('ai_chats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_messages');
    }
};
