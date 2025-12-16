<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_products', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('product_type_id')->default(1)->comment('1:人工芝、2:副資材, 3:販促商品');
            $table->string('name')->unique('name');
            $table->integer('price')->comment('反物一般価格');
            $table->integer('whole_price')->nullable()->comment('卸売反物価格');
            $table->integer('fc_price')->nullable()->comment('FCへの反物販売価格');
            $table->integer('cut_price')->nullable()->comment('反物切り売り価格');
            $table->integer('cut_whole_price')->nullable()->comment('卸売切り売り価格');
            $table->integer('cut_fc_price')->nullable()->comment('FCへの切り売り販売価格');
            $table->integer('stock')->comment('在庫数');
            $table->integer('stock_high')->comment('余裕ある在庫数（青）以上');
            $table->tinyInteger('stock_low')->comment('少ない在庫数（赤）以下');
            $table->string('unit')->default('反')->comment('まとめ売り商品単位');
            $table->string('cut_unit')->nullable()->comment('切り売り・バラ売り商品単位');
            $table->float('horizontal')->nullable()->comment('切り売り・横幅');
            $table->float('vertical')->nullable()->comment('切り売り・縦幅');
            $table->string('specification')->comment('仕様');
            $table->string('material')->comment('素材')->nullable();
            $table->string('characteristic', 1000)->comment('特徴')->nullable();
            $table->string('image')->comment('販促物見本画像')->nullable();
            $table->tinyInteger('is_use_quotation')->default(0)->comment('1なら見積書作成に出てくる');
            $table->tinyInteger('is_same_cut_price')->default(0)->comment('1ならまとめ売りと切り売りが同じ価格で、バラ売りにもまとめ売りにも出現する');
            $table->integer('order_no')->comment('並び順');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('freee_item_id')->nullable()->comment('freee品目ID');
            $table->string('url')->nullable()->comment('資料ダウンロードリンク');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_products');
    }
}
