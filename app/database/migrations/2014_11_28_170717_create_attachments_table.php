<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttachmentsTable extends Migration {

    public function up() {
        Schema::create('attachments', function(Blueprint $table) {
            //$table->engine = 'INNODB';
            $table->increments('id');
            $table->string('origFileName', 128)->nullable();
            $table->string('savedFileName', 128);
            $table->bigInteger('fileSize')->unsigned();
            $table->bigInteger('task_id')->unsigned()->index()->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->timestamp('upload_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('attachments');
    }

}
