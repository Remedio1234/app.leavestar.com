<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('logo', 1024)->nullable();
            $table->string('timezone', 255)->nullable();
            $table->integer('leave_rules')->nullable();
            $table->integer('leave_capacity')->nullable();
 
            

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
