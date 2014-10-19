<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskPriorityTable extends Migration {

	public function up()
	{
		Schema::create('task_priority', function(Blueprint $table) {
			$table->bigInteger('task_id')->unsigned();
			$table->integer('priority')->unique()->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('task_priority');
	}
}