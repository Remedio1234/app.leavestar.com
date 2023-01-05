<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('comment', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('leave_id')->unsigned();
            $table->index('leave_id', 'leave_id');
            $table->foreign('leave_id')->references('id')->on('leave_application')->onDelete('RESTRICT');

            $table->integer('sender_id')->unsigned();
            $table->index('sender_id', 'sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('RESTRICT');

            $table->string('content', 1024);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
