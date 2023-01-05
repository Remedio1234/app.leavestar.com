<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeaveApplicationTable1 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('leave_application', function (Blueprint $table) {
//            $table->integer('autoreplysetting')->default(0)->after('xero_id');
//            $table->string('autoreplymessage')->nullable()->after('autoreplysetting');
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
