<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreign('contact_id', 'quotations_ibfk_1')->references('id')->on('contacts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('user_id', 'quotations_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign('quotations_ibfk_1');
            $table->dropForeign('quotations_ibfk_2');
        });
    }
}
