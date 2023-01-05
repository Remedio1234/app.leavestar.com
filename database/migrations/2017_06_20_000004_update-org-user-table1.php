<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrgUserTable1 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organisation_user', function (Blueprint $table) {

            $table->string('is_admin')->default('no')->after('user_id');
            $table->string('phone', 50)->nullable()->after('is_admin');
            $table->string('address', 2000)->nullable()->after('phone');
            $table->integer('leave_left')->nullable()->after('address');
            $table->timestamp('birthday')->after('leave_left');
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
