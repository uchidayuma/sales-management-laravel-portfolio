<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionCommen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
                $table->integer('transaction_only_shipping_id')->nullable()->after('total')->comment('案件に紐付かない発注 or 追加発注発注の業者ID（ヤマト、佐川など）')->change();
                $table->string('transaction_only_shipping_number')->nullable()->after('total')->comment('案件に紐付かない or 追加発注発注の追跡番号 ')->change();
                $table->dateTime('transaction_only_shipping_date')->nullable()->after('total')->comment('案件に紐付かない or 追加発注発注の発送日')->change();
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
                $table->integer('transaction_only_shipping_id')->nullable()->after('total')->comment('案件に紐付かない発注の業者ID（ヤマト、佐川など）')->change();
                $table->string('transaction_only_shipping_number')->nullable()->after('total')->comment('案件に紐付かない発注の追跡番号')->change();
                $table->dateTime('transaction_only_shipping_date')->nullable()->after('total')->comment('案件に紐付かない発注の発送日')->change();
        });
    }
}
