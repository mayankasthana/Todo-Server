<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Task_assignee extends Eloquent {

	protected $table = 'task_user_assign';
	public $timestamps = true;

	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];

        public function task(){
            return $this->belongsTo('Task');
        }
        
        public function user(){
            return $this->belongsTo('User');
        }
}