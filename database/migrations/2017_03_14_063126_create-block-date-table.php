<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockDateTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('block_date', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('setting_id')->unsigned();
            $table->index('setting_id', 'setting_id');
            $table->foreign('setting_id')->references('id')->on('setting')->onDelete('RESTRICT');

            $table->timestamp('start_date')->default(null);
            $table->timestamp('end_date')->default(null);
            $table->integer('limits')->comment('0--no one can take leave, 1+---number of people could take leave');
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
