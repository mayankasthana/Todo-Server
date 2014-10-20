<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

	public function up()
	{
		Schema::create('tasks', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->timestamps();
			$table->softDeletes();
			$table->bigInteger('created_by_user_id')->unsigned();
			$table->text('text');
			$table->string('status', 20)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('tasks');
	}
}