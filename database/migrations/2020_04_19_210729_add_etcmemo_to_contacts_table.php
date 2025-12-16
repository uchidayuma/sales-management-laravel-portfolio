<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEtcmemoToContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('contacts', 'etc_memo')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->json('etc_memo')->nullable()->after('memo')->comment('import時のその他項目をまとめるフィールド');
                $table->string('zipcode', 11)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('contacts', 'etc_memo')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('etc_memo');
                $table->integer('zipcode')->charset(null)->collation(null)->change();
            });
        }
    }
}
