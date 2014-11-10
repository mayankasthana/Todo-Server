<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskUserAssignTable extends Migration {

    public function up() {
        Schema::create('task_user_assign', function(Blueprint $table) {
           // $table->engine = 'INNODB';
            $table->bigIncrements('id');
            $table->bigInteger('task_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::drop('task_user_assign');
    }

}
