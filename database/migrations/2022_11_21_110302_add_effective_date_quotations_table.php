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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('effective_date')->nullable()->after('discount')->default('1ヶ月')->comment('見積書自体の有効期限');
            $table->string('payee',500)->nullable()->after('memo')->comment('振込先');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('effective_date');
            $table->dropColumn('payee');
        });
    }
};
