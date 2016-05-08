<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_id')->unsigned()->index();
            $table->foreign('sender_id')->references('id')->on('users');
            $table->integer('recipient_id')->unsigned()->index();
            $table->foreign('recipient_id')->references('id')->on('users');
            $table->boolean('active')->default(false);
            $table->boolean('blocked')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('favorite')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('conversations');
    }
}
