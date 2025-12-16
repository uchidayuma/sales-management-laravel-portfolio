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
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedInteger('registered_user_id')->default(null)->nullable()->after('main_user_id')->comment('案件を登録したFCID(本部に全委任した場合の記録用)');
			$table->foreign('registered_user_id', 'users_ibfk_registered_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
        Schema::table('contacts', function (Blueprint $table) {
			$table->dropForeign('users_ibfk_registered_user_id');
            $table->dropColumn('registered_user_id');
        });
        Schema::enableForeignKeyConstraints();
    }
};
