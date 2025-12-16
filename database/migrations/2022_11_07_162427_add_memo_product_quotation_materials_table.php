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
        Schema::table('product_quotation_materials', function (Blueprint $table) {
            $table->text('memo')->after('other_product_name')->nullable()->comment('各行の備考欄');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_quotation_materials', function (Blueprint $table) {
            $table->dropColumn('memo');
        });
    }
};
