<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLeaveCapacityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('leave_capacity', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('org_id')->unsigned()->nullable();

            $table->integer('leave_type_id')->unsigned();
            $table->index('leave_type_id', 'leave_type_id');
            $table->foreign('leave_type_id')->references('id')->on('leave_type')->onDelete('RESTRICT');

            $table->integer('capacity')->default(0);

            $table->timestamp('last_update_date')->default(null)->nullable();

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
