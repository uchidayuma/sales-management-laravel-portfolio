<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnlyToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('transaction_only_shipping_id')->nullable()->after('total')->comment('案件に紐付かない発注の業者ID（ヤマト、佐川など）');
            $table->string('transaction_only_shipping_number')->nullable()->after('total')->comment('案件に紐付かない発注の追跡番号');
            $table->dateTime('transaction_only_shipping_date')->nullable()->after('total')->comment('案件に紐付かない発注の発送日');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_only_shipping_id');
            $table->dropColumn('transaction_only_shipping_number');
            $table->dropColumn('transaction_only_shipping_date');
        });
    }
}
