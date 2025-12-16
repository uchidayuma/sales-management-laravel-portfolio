<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkUserNotifications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->foreign('contact_id', 'user_notifications_ibfk_2')->references('id')->on('contacts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropForeign('user_notifications_ibfk_2');
        });
    }
}
