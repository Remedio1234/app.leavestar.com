<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);

            
            $table->integer('setting_id')->unsigned()->default(1);
            $table->index('setting_id', 'setting_id');
            $table->foreign('setting_id')->references('id')->on('setting')->onDelete('RESTRICT');


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
