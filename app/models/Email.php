<?php

class Email extends Eloquent {

    protected $table = 'emails';
    public $timestamps = true;
    protected $hidden = array('updated_at');

    public static function deferSend($from, $toEmailId, $type, $subject, $body, $timeToSend) {
        $email = new Email;
        $email->origin = $from;
        $email->to_email_id = $toEmailId;
        $email->type = $type;
        $email->subject = $subject;
        $email->body = $body;
        $email->time_to_send = $timeToSend;
        $email->save();
    }

}
