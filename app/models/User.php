<?php

class User extends Eloquent {

	protected $table = 'users';
	public $timestamps = true;
	protected $hidden = array('salt', 'register_ip', 'forget_token', 'active_token');

	public function role()
	{
		return $this->belongsToMany('Role');
	}

	public function attempts()
	{
		return $this->hasMany('Login_attempts');
	}

}