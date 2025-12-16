<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSippingNumberToContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('shipping_number', 150)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            // $table->bigInteger('shipping_number')->unsigned()->nullable()->comment('お問い合わせ番号')->change();
            $table->bigInteger('shipping_number')->unsigned()->nullable()->charset(null)->comment('問い合わせ番号')->change();
        });
    }
}
