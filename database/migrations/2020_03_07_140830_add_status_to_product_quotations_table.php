<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToProductQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('product_quotations', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('price')->comment('編集で削除した場合出さないようにするためのstatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('product_quotations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
