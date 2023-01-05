<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('last_visit_org');
            $table->string('address', 2000)->after('phone');
            $table->integer('leave_left')->after('address');
            $table->timestamp('birthday')->after('leave_left');
            $table->string('xero_id', 2000)->nullable()->after('birthday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('users');
    }

}
