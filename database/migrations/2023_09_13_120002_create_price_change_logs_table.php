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
        Schema::create('price_change_logs', function (Blueprint $table) {
                    // log_id INT AUTO_INCREMENT PRIMARY KEY,
                    // product_id INT,
                    // field_name VARCHAR(255),
                    // old_value INT,
                    // new_value INT,
                    // change_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('field_name');
            $table->integer('old_value');
            $table->integer('new_value');
            $table->timestamp('change_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->comment('商品価格が変更された時に記録するテーブル→MariaDBのトリガーでチェックするため');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_change_logs');
    }
};
