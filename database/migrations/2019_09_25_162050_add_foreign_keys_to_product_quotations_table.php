<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProductQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('product_quotations', function (Blueprint $table) {
            $table->foreign('product_id', 'product_quotations_ibfk_1')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('product_quotations', function (Blueprint $table) {
            $table->dropForeign('product_quotations_ibfk_1');
        });
    }
}
