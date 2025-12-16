<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('delivery_at2')->nullable()->after('delivery_at');
            $table->date('delivery_at3')->nullable()->after('delivery_at2');
            $table->integer('shipping_id2')->nullable()->after('transaction_only_shipping_id');
            $table->string('shipping_number2')->nullable()->after('shipping_id2');
            $table->datetime('shipping_date2')->nullable()->after('shipping_number2');
            $table->string('dispatch_message2', 2000)->nullable()->after('shipping_date2');
            $table->integer('shipping_id3')->nullable()->after('shipping_date2');
            $table->string('shipping_number3')->nullable()->after('shipping_id3');
            $table->datetime('shipping_date3')->nullable()->after('shipping_number3');
            $table->string('dispatch_message3', 2000)->nullable()->after('shipping_date3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('delivery_at2');
            $table->dropColumn('delivery_at3');
            $table->dropColumn('shipping_id2');
            $table->dropColumn('shipping_number2');
            $table->dropColumn('shipping_date2');
            $table->dropColumn('dispatch_message2');
            $table->dropColumn('shipping_id3');
            $table->dropColumn('shipping_number3');
            $table->dropColumn('shipping_date3');
            $table->dropColumn('dispatch_message3');
        });
    }
}
