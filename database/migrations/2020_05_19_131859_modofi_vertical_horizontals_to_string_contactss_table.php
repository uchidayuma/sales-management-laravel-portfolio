<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModofiVerticalHorizontalsToStringContactssTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('vertical_size', 11)->nullable()->comment('施工場所面積の縦')->change();
            $table->string('horizontal_size', 11)->nullable()->comment('施工場所面積の横')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->integer('vertical_size')->nullable()->charset(null)->collation(null)->comment('縦')->change();
            $table->integer('horizontal_size')->nullable()->charset(null)->collation(null)->comment('横')->change();
        });
    }
}
