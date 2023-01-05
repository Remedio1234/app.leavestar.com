<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class CreateOrgnazationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('organisation_structure', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);

            NestedSet::columns($table);
            $table->integer('setting_id')->unsigned()->nullable();
            $table->index('setting_id', 'setting_id');
            $table->foreign('setting_id')->references('id')->on('setting')->onDelete('RESTRICT');

            $table->integer('setting_new')->unsigned()->nullable()->default(0)->comment('0---not new(need create new if update) , 1-- new');

            $table->integer('account_id')->unsigned();
            $table->index('account_id', 'account_id');
            $table->foreign('account_id')->references('id')->on('account')->onDelete('RESTRICT');

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
