<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteIdToSameCustomerContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('same_customer_contacts', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('same_customer_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }
}
