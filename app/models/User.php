<?php

class User extends Eloquent {

    protected $table = 'users';
    public $timestamps = true;
    protected $hidden = array('salt', 'register_ip', 'forget_token', 'active_token');

    public function role() {
        return $this->belongsToMany('Role');
    }
    public function comments(){
	$this->hasMany('Comment','user_id');
    }
    public function attempts() {
        return $this->hasMany('Login_attempts');
    }

    public function tasks() {
        return $this->belongsToMany('Task');
    }

    public static function saveGPlusUser($user) {
        // $user->
        $newUser = new User;
        $newUser->ouid = $user->id;
        $newUser->displayName = $user->displayName;
        $newUser->email = $user->emails[0]->value;
        $newUser->save();
    }

    public static function isUserWithOuidExists($ouid) {
        try {
            User::where('ouid', '=', $ouid)->firstOrFail();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
