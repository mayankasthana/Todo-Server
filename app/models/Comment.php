<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Comment extends Eloquent {

	protected $table = 'comments';
	public $timestamps = true;
        public $hidden = array(
            'updated_at',
            'deleted_at'
        );
	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];

        public function commentor(){
            $this->belongsTo('User','user_id');
        }
        
        public function task(){
            $this->belongsTo('Task','task_id');
        }
}