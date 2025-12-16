<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name')->default('見積書')->comment('見積書自体の名前');
            $table->string('client_name')->default('株式会社御中')->comment('見積相手の名前');
            $table->unsignedBigInteger('contact_id');
            $table->unsignedInteger('user_id');
            $table->string('memo')->nullable()->comment('備考欄');
            $table->integer('sub_total')->comment('小計');
            $table->integer('total')->comment('消費税込みの合計金額');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('quotations');
    }
}
