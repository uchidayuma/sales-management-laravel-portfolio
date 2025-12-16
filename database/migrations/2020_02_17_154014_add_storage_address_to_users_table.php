<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStorageAddressToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('s_zipcode')->after('staff_ruby');
            $table->string('s_pref')->after('s_zipcode');
            $table->string('s_city')->after('s_pref');
            $table->string('s_street')->after('s_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn('s_zipcode');
             $table->dropColumn('s_pref');
             $table->dropColumn('s_city');
             $table->dropColumn('s_street');
        });
    }
}
