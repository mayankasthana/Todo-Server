<?php

class Notification extends Eloquent {

    protected $table = 'notifications';
    public $timestamps = true;
    protected $hidden = array('updated_at');

    public static function notify($toUserId, $message, $type = null, $origin = null) {
        $notification =  new Notification;
        $notification->origin = $origin;
        $notification->to_user_id = $toUserId;
        $notification->message = $message;
        $notification->type = $type;
        $notification->save();
    }

}
