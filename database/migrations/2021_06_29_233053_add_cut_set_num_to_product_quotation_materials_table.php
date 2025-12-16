<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCutSetNumToProductQuotationMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_quotation_materials', function (Blueprint $table) {
            $table->tinyInteger('cut_set_num')->nullable()->after('num');
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
            $table->dropColumn('cut_set_num');
        });
    }
}
