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
        Schema::create('area_open_email_sends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->tinyinteger('send_status')->default(1)->comment('0=送る予定から送らないに変更、1=送る予定と送った');
            $table->tinyinteger('status')->default(1)->comment('1=送る予定と送った,2=送信済み');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->foreign('user_id', 'users_ibfk')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('area_open_email_sends', function (Blueprint $table) {
			$table->dropForeign('users_ibfk');
        });
        Schema::enableForeignKeyConstraints();
        Schema::dropIfExists('area_open_email_sends');
    }
};
