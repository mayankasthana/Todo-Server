<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailsTable extends Migration {

    public function up() {
        Schema::create('emails', function(Blueprint $table) {
            //$table->engine = 'INNODB';
            $table->increments('id');
            $table->timestamps();
            $table->string('origin', 64)->nullable();
            $table->string('to_email_id',128);
            $table->string('type', 64);
            $table->string('subject', 512);
            $table->text('body');
            $table->timestamp('time_to_send')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('send_status')->default(false);
            $table->timestamp('sent_time')->nullable();
        });
    }

    public function down() {
        Schema::drop('notifications');
    }

}
