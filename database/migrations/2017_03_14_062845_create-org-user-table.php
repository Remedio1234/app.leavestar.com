<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrgUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('organisation_user', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('org_str_id')->unsigned();
            $table->index('org_str_id', 'org_str_id');
            $table->foreign('org_str_id')->references('id')->on('organisation_structure')->onDelete('RESTRICT');

            $table->integer('user_id')->unsigned();
            $table->index('user_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('RESTRICT');

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
