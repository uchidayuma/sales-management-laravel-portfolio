<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginalNamesToContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('document1_original_name')->after('document1')->comment('元のファイル名')->nullable();
            $table->string('document2_original_name')->after('document2')->comment('元のファイル名')->nullable();
            $table->string('document3_original_name')->after('document3')->comment('元のファイル名')->nullable();
            $table->string('document4_original_name')->after('document4')->comment('元のファイル名')->nullable();
            $table->string('document5_original_name')->after('document5')->comment('元のファイル名')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('document1_original_name');
            $table->dropColumn('document2_original_name');
            $table->dropColumn('document3_original_name');
            $table->dropColumn('document4_original_name');
            $table->dropColumn('document5_original_name');
        });
    }
}
