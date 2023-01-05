<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAccountingTokenTable2 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('accounting_token', function (Blueprint $table) {
            $table->string('xero_org_name',1000)->nullable()->after('refresh_token');
            $table->string('last_check_time',1000)->nullable()->after('xero_org_name');
           
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
