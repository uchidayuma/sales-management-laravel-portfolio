<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProductTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('product_transactions', function(Blueprint $table)
		{
			$table->foreign('product_id', 'product_transactions_ibfk_1')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('transaction_id', 'product_transactions_ibfk_2')->references('id')->on('transactions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('product_transactions', function(Blueprint $table)
		{
			$table->dropForeign('product_transactions_ibfk_1');
			$table->dropForeign('product_transactions_ibfk_2');
		});
	}

}
