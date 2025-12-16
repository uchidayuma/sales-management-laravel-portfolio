<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToNotificationTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('notification_types', function(Blueprint $table)
		{
			$table->foreign('step_id', 'notification_types_ibfk_1')->references('id')->on('steps')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('notification_types', function(Blueprint $table)
		{
			$table->dropForeign('notification_types_ibfk_1');
		});
	}

}
