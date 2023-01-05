<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizedFeedTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('customized_feed', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('org_id')->unsigned();
            $table->index('org_id', 'org_id');
            $table->foreign('org_id')->references('id')->on('organisation_structure')->onDelete('RESTRICT');

            $table->integer('user_id')->unsigned();
            $table->index('user_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('RESTRICT');

            $table->string('feed', 3000)->nullable();

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
