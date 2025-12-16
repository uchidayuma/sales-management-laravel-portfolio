<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique('email');
            $table->string('email2')->nullable()->comment('FC社内通知用アドレス');
            $table->string('email3')->nullable()->comment('FC社内通知用アドレス');
            $table->string('password')->nullable();
            $table->string('company_name')->unique();
            $table->string('company_ruby')->nullable();
            $table->integer('zipcode');
            $table->string('pref');
            $table->string('city');
            $table->string('street');
            $table->double('latitude');
            $table->double('longitude');
            $table->bigInteger('tel');
            $table->bigInteger('fax')->nullable();
            $table->string('staff');
            $table->string('staff_ruby')->nullable();
            $table->unsignedInteger('rank_id')->nullable();
            $table->string('seal')->nullable()->comment('印鑑（笑）用のカラム');
            $table->boolean('admin')->nullable()->default(2)->comment('管理者=1, FC=2');
            $table->boolean('status')->nullable()->default(3)->comment('アクティブ=1, 削除済み=2, 研修中＝3');
            $table->string('remember_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('users');
    }
}
