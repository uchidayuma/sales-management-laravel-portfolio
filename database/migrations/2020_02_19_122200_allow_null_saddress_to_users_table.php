<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullSaddressToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('s_zipcode')->nullable()->change();
            $table->string('s_pref')->nullable()->change();
            $table->string('s_city')->nullable()->change();
            $table->string('s_street')->nullable()->change();
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
            $table->integer('s_zipcode')->nullable(false)->change();
            $table->string('s_pref')->nullable(false)->change();
            $table->string('s_city')->nullable(false)->change();
            $table->string('s_street')->nullable(false)->change();
        });
    }
}
