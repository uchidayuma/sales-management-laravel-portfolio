<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('tel')->nullable()->after('address')->comment('お届け先電話番号');
            $table->date('delivery_at')->nullable()->after('tel')->comment('お届け先電話番号');
            $table->longText('edit_dom')->nullable()->after('delivery_at')->comment('編集時のDOM保存用');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('tel');
            $table->dropColumn('delivery_at');
            $table->dropColumn('edit_dom');
        });
    }
}
