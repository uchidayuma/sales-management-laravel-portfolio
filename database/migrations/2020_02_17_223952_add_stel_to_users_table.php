<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStelToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('s_tel')->nullable()->after('tel');
            $table->string('tel')->change();
            $table->string('fax')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('s_tel');
            $table->bigInteger('tel')->charset(null)->change();
            $table->bigInteger('fax')->charset(null)->nullable()->change();
        });
    }
}
