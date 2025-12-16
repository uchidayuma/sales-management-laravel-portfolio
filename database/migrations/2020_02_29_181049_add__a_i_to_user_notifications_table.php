<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAIToUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            DB::statement('ALTER TABLE user_notifications MODIFY id INT NOT NULL PRIMARY KEY AUTO_INCREMENT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            DB::statement('ALTER TABLE user_notifications MODIFY id INT NOT NULL');
            DB::statement('ALTER TABLE user_notifications DROP PRIMARY KEY');
            DB::statement('ALTER TABLE user_notifications MODIFY id INT NULL');
        });
    }
}
