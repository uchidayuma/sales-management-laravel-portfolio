<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('user_id', 'contacts_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('shipping_id', 'contacts_ibfk_2')->references('id')->on('shippings')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('step_id', 'contacts_ibfk_3')->references('id')->on('steps')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('contact_type_id', 'contacts_ibfk_4')->references('id')->on('contact_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('quotation_id', 'contacts_ibfk_5')->references('id')->on('quotations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign('contacts_ibfk_1');
            $table->dropForeign('contacts_ibfk_2');
            $table->dropForeign('contacts_ibfk_3');
            $table->dropForeign('contacts_ibfk_4');
            $table->dropForeign('contacts_ibfk_5');
        });
    }
}
