<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveAccrualSettingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('leave_accrual_setting', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('org_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->integer('leave_type_id')->unsigned();
            $table->index('leave_type_id', 'leave_type_id');
            $table->foreign('leave_type_id')->references('id')->on('leave_type')->onDelete('RESTRICT');

            $table->integer('period')->default(0);
            $table->bigInteger('seconds')->nullable();
            $table->integer('options')->default(0);
            $table->string('is_new')->default(1);

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
