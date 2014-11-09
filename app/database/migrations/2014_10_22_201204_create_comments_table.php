<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration {

    public function up() {
        Schema::create('comments', function(Blueprint $table) {
            //$table->engine = 'INNODB';
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('text', 500);
            $table->bigInteger('task_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();
        });
    }

    public function down() {
        Schema::drop('comments');
    }

}
