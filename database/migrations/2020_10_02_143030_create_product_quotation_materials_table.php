<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductQuotationMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 発注書ライクな見積書用テーブル
        Schema::create('product_quotation_materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('quotation_id');
            $table->float('vertical')->nullable()->comment('切り売り縦幅');
            $table->float('horizontal')->nullable()->comment('切り売り横幅');
            $table->string('unit')->default('m2')->comment('単位');
            $table->integer('unit_price')->default(0)->comment('単価');
            $table->float('num')->nullable()->default(1);
            // $table->integer('price')->default(0)->comment('単位＊単価');
            $table->json('turf_cuts')->nullable()->comment('カット人工芝メニューに付随するカットメニュー');
            $table->tinyInteger('cut')->default(0)->comment('1なら切り売り');
            $table->string('other_product_name')->nullable()->comment('productsテーブルにない独自注文の名称');
            // $table->integer('other_product_price')->nullable()->comment('productsテーブルにない独自注文の価格');
            $table->tinyInteger('status')->default(1)->comment('1=アクティブ：2=削除');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_quotation_materials');
    }
}
