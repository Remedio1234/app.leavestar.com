<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingTokenTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('accounting_token', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('org_str_id')->unsigned();
            $table->index('org_str_id', 'org_str_id');
            $table->foreign('org_str_id')->references('id')->on('organisation_structure')->onDelete('RESTRICT');

            $table->integer('accsoft_id')->unsigned();
            $table->index('accsoft_id', 'accsoft_id');
            $table->foreign('accsoft_id')->references('id')->on('accounting_software')->onDelete('RESTRICT');


            $table->text('token');
            $table->text('secret_token');
            $table->text('refresh_token');

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
