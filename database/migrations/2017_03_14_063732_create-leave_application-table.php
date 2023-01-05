<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveApplicationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('leave_application', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->index('user_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('RESTRICT');

            $table->integer('org_id')->unsigned();
            $table->index('org_id', 'org_id');
            $table->foreign('org_id')->references('id')->on('organisation_structure')->onDelete('RESTRICT');

            $table->timestamp('start_date');
            $table->timestamp('end_date');

            $table->integer('leave_type_id')->unsigned();
            $table->index('leave_type_id', 'leave_type_id');
            $table->foreign('leave_type_id')->references('id')->on('leave_type')->onDelete('RESTRICT');

            $table->integer('flexible')->comment('0---not flexible , 1-- flexible');
            $table->string('status', 255);

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
