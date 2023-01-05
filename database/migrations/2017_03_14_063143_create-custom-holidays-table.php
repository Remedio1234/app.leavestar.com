<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomHolidaysTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('custom_holidays', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('setting_id')->unsigned();
            $table->index('setting_id', 'setting_id');
            $table->foreign('setting_id')->references('id')->on('setting')->onDelete('RESTRICT');

            $table->timestamp('date');
            $table->string('name', 255);
            $table->text('description')->nullable();

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
