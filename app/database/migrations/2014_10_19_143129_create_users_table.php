<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->timestamps();
			$table->string('displayName', 30);
                        $table->string('ouid',50);
			$table->string('email', 100);
			//$table->string('register_ip', 15);
			$table->string('last_token', 100)->nullable();
			$table->string('access_token', 100)->nullable();
                        $table->integer('access_token_time')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('users');
	}
}