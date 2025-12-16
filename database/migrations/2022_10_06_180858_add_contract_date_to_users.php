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
        Schema::table('users', function (Blueprint $table) {
            $table->date('contract_date')->nullable()->default(null)->after('email_verified_at')->comment('FCの契約日');
            $table->unsignedBigInteger('fc_apply_area_id')->default(null)->nullable()->after('apply_area')->comment('FCの担当エリアID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('contract_date');
            $table->dropColumn('fc_apply_area_id');
        });
    }
};
