<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskUserTable extends Migration {

	public function up()
	{
		Schema::create('task_user', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->timestamps();
			$table->softDeletes();
			$table->bigInteger('task_id')->unsigned()->index();
			$table->bigInteger('user_id')->unsigned()->index();
		});
	}

	public function down()
	{
		Schema::drop('task_user');
	}
}