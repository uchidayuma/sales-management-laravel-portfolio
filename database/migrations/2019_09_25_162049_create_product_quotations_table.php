<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('quotation_id')->comment('案件ごとに見積もりは複数出る可能性があるので、見積もりIDとJOINするケロ');
            $table->unsignedInteger('product_id')->nullable();
            $table->integer('num')->default(1);
            $table->string('unit')->default('m2')->comment('単位');
            $table->integer('unit_price')->comment('単価（1つあたりの個数）');
            $table->boolean('other_product')->nullable()->default(0)->comment('商品以外の自由記述なら1');
            $table->string('name')->nullable()->comment('自由記述の商品名');
            $table->integer('price')->nullable()->comment('単価＊個数');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('product_quotations');
    }
}
