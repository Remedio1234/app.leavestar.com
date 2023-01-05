<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAccountingTokenTable3 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('accounting_token', function (Blueprint $table) {
             $table->string('earingrate_id',2000)->nullable()->after('xero_org_name');
              $table->string('calendar_id',2000)->nullable()->after('earingrate_id');
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