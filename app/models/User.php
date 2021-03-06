<?php

use Carbon\Carbon;

class User extends Eloquent {

    protected $table = 'users';
    public $timestamps = true;
    protected $hidden = array(
        'salt',
        'register_ip',
        'forget_token',
        'active_token',
        'created_at',
        'updated_at',
        'last_token',
        'access_token',
        'pivot'
        );

    public function role() {
        return $this->belongsToMany('Role');
    }

    public function comments() {
        $this->hasMany('Comment', 'user_id');
    }

    public function attempts() {
        return $this->hasMany('Login_attempts');
    }

    public function tasks() {
        return $this->belongsToMany('Task');
    }

    public function assignedTasks() {
        return $this->belongsToMany('Task', 'task_user_assign', 'user_id', 'task_id');
    }

    public static function saveGPlusUser($user) {
        // $user->
        $newUser = new User;
        $newUser->ouid = $user->id;
        $newUser->displayName = $user->displayName;
        $newUser->email = $user->emails[0]->value;
        $newUser->profilePic =$user->getImage()->getUrl();
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

    public static function IsValidAccessToken($accessToken) {
        $user = User::where('access_token', $accessToken)->get(array('id', 'access_token_time'))->toArray();
        //return Carbon::createFromTimeStamp($user->access_token_time);
        if (sizeof($user) == 0) {
            return false;
        }
        $timeDiff = Carbon::now()->diffInSeconds(Carbon::createFromTimeStamp($user[0]['access_token_time']));
        if ($timeDiff > 3600) {
            return false;
        }
        return true;
    }

    public function myTasks() {
        return User::associatedTasks($this->id);
    }

    public static function associatedTasks($userId) {
        //DB::table('task_user')
        $tasks = Task::where('created_by_user_id', $userId)
                ->whereNull('deleted_at')
                ->orWhereHas('users', function($q) use($userId) {
            $q->where('user_id', $userId)
            ->whereNull('deleted_at');
        });
        return $tasks;
    }

}
