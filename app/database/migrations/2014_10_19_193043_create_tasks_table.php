<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

    public function up() {
        Schema::create('tasks', function(Blueprint $table) {
           // $table->engine = 'INNODB';
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('created_by_user_id')->unsigned()->index();
            $table->string('title',200);
            $table->text('description');
            $table->date('deadlinedate')->nullable();
            $table->time('deadlinetime')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('status');
            $table->tinyInteger('priority')->unsigned()->default(1);
        });
    }

    public function down() {
        Schema::drop('tasks');
    }

}
