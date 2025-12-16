<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductIdToProductTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('product_transactions', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->nullable()->change();
            $table->string('other_product_name')->nullable()->after('cut')->comment('productsテーブルにない独自注文の名称');
            $table->integer('other_product_price')->nullable()->after('cut')->comment('productsテーブルにない独自注文の価格');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('product_transactions', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->change();
            $table->dropColumn('other_product_name');
            $table->dropColumn('other_product_price');
        });
    }
}
