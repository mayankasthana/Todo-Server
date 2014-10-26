<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

    public function up() {
        Schema::create('notifications', function(Blueprint $table) {
            $table->engine = 'INNODB';
            $table->increments('id');
            $table->timestamps();
            $table->string('origin', 20)->nullable();
            $table->bigInteger('to_user_id')->unsigned()->index()->nullable();
            $table->string('message', 140);
            $table->string('type', 10);
            $table->timestamp('seen_time')->nullable();
        });
    }

    public function down() {
        Schema::drop('notifications');
    }

}
