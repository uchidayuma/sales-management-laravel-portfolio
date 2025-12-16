<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('shipping_include')->default(1)->after('order_no')->comment('0: 同梱不可, 1: 同梱OK');
            $table->float('shipping_weight', 4, 2)->nullable()->after('order_no')->comment('小サイズに入る商品の重量');
            $table->tinyInteger('shipping_size')->default(1)->after('order_no')->comment('0: カウントしない, 1: 小, 2: 大 or 特大');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shipping_weight', 'shipping_size', 'shipping_include']);
        });
    }
};
