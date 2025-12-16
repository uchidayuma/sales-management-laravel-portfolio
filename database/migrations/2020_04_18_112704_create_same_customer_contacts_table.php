<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSameCustomerContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('same_customer_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('contact_id')->comment('条件の一致した問い合わせのid');
            $table->biginteger('ref_contact_id')->comment('自身のid');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('same_customer_contacts');
    }
}
