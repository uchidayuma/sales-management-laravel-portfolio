<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('storage_tel')->after('s_street')->nullable()->comment('資材置き場担当者の電話番号');
            $table->integer('optional_zipcode')->after('storage_tel')->nullable()->comment('任意受け取り場所の郵便番号');
            $table->string('optional_pref')->after('optional_zipcode')->nullable()->comment('任意受け取り場所の都道府県');
            $table->string('optional_city')->after('optional_pref')->nullable()->comment('任意受け取り場所の市町村');
            $table->string('optional_street')->after('optional_city')->nullable()->comment('任意受け取り場所の市町村以下');
            $table->string('optional_tel')->after('optional_street')->nullable()->comment('任意受け取り場所の電話番号');
            $table->string('optional_staff')->after('optional_street')->nullable()->comment('任意受け取り場所の受け取り人');
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
            $table->dropColumn('storage_tel');
            $table->dropColumn('optional_zipcode');
            $table->dropColumn('optional_pref');
            $table->dropColumn('optional_city');
            $table->dropColumn('optional_street');
            $table->dropColumn('optional_tel');
            $table->dropColumn('optional_staff');
        });
    }
}
