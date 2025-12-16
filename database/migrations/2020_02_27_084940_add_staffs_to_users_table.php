<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('staff2')->nullable()->after('staff_ruby');
            $table->string('staff2_ruby')->nullable()->after('staff2');
            $table->string('staff3')->nullable()->after('staff2_ruby');
            $table->string('staff3_ruby')->nullable()->after('staff3');
            $table->string('s2_tel')->nullable()->after('s_tel');
            $table->string('s3_tel')->nullable()->after('s2_tel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('staff2');
            $table->dropColumn('staff2_ruby');
            $table->dropColumn('staff3');
            $table->dropColumn('staff3_ruby');
            $table->dropColumn('s2_tel');
            $table->dropColumn('s3_tel');
        });
    }
}
