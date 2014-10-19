<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Task_members extends Eloquent {

	protected $table = 'task_user';
	public $timestamps = true;

	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];

}