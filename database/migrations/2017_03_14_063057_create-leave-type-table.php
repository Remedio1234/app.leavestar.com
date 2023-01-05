<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTypeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('leave_type', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('setting_id')->unsigned();
            $table->index('setting_id', 'setting_id');
            $table->foreign('setting_id')->references('id')->on('setting')->onDelete('RESTRICT');

            $table->string('name', 255);
            $table->string('description', 1024)->nullable();

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
