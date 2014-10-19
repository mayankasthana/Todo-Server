<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Task extends Eloquent {

	protected $table = 'tasks';
	public $timestamps = true;

	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];
	protected $hidden = array('softDeletes');

	public function users()
	{
		return $this->belongsToMany('User');
	}

	public function creator()
	{
		return $this->belongsTo('User', 'created_by_user_id');
	}
        
        public function priority()
	{
		return $this->hasOne('Task_priority', 'task_id');
	}
}