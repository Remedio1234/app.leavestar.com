<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRegisterTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_register', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('org_id');
            $table->string('is_admin')->default('no');
            $table->string('token', 3000);
            $table->string('email');
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->timestamp('birthday');
            $table->string('xero_id');

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
