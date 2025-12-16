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
        Schema::table('regions', function (Blueprint $table) {
            $table->integer('small_shipping_price')->after('name')->comment('小サイズ1箱の送料');
            $table->integer('large_shipping_price')->after('small_shipping_price')->comment('大サイズ1箱の送料');
            $table->integer('extra_large_shipping_price')->after('large_shipping_price')->comment('特大サイズ1箱の送料');
            $table->integer('extra_large_shipping_price2')->after('extra_large_shipping_price')->comment('特大サイズ1箱の送料(2反購入した時の1反あたりの送料)');
            $table->integer('extra_large_shipping_price3')->after('extra_large_shipping_price2')->comment('特大サイズ1箱の送料(3反購入した時の1反あたりの送料)');
            $table->timestamp('updated_at')->after('extra_large_shipping_price3')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->after('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn([
                'small_shipping_price',
                'large_shipping_price',
                'extra_large_shipping_price',
                'extra_large_shipping_price2',
                'extra_large_shipping_price3',
            ]);
        });
    }
};
