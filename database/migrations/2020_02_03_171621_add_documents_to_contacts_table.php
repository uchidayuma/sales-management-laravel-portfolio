<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentsToContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
            $table->string('document1')->nullable()->comment('添付資料1');
            $table->string('document2')->nullable()->comment('添付資料2');
            $table->string('document3')->nullable()->comment('添付資料3');
            $table->string('document4')->nullable()->comment('添付資料4');
            $table->string('document5')->nullable()->comment('添付資料5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
            $table->dropColumn('document1');
            $table->dropColumn('document2');
            $table->dropColumn('document3');
            $table->dropColumn('document4');
            $table->dropColumn('document5');
        });
    }
}
