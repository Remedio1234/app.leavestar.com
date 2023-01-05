<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAccountTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('account', function (Blueprint $table) {
            $table->string('stripe_client_token', 2000)->nullable()->after('setting_id');
            $table->string('stripe_sub_token', 2000)->nullable()->after('stripe_client_token');
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
