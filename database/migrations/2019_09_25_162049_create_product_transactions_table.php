<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('transaction_id');
            $table->float('vertical')->nullable()->comment('切り売り縦幅');
            $table->float('horizontal')->nullable()->comment('切り売り横幅');
            $table->string('unit')->default('m2')->comment('単位');
            $table->integer('num')->nullable()->default(1);
            $table->json('turf_cuts')->nullable()->comment('カット人工芝メニューに付随するカットメニュー');
            $table->tinyInteger('cut')->default(0)->comment('1なら切り売り');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('product_transactions');
    }
}
