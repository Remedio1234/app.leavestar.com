<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeaveTypeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('leave_type', function (Blueprint $table) {
            $table->dropForeign(['setting_id']);
            $table->dropIndex('setting_id');
            $table->dropColumn('setting_id');

            $table->integer('org_id')->unsigned()->after('description')->nullable();
            $table->index('org_id', 'org_id');
            $table->foreign('org_id')->references('id')->on('organisation_structure')->onDelete('RESTRICT');

            $table->string('xero_id', 2000)->after('org_id')->nullable();
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
