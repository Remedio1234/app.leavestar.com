<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegisterCapacityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('register_capacity', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('register_id')->unsigned();
            $table->index('register_id', 'register_id');
            $table->foreign('register_id')->references('id')->on('user_register')->onDelete('RESTRICT');

            $table->integer('leave_type_id')->unsigned();
            $table->index('leave_type_id', 'leave_type_id');
            $table->foreign('leave_type_id')->references('id')->on('leave_type')->onDelete('RESTRICT');

            $table->integer('capacity')->default(0);

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
