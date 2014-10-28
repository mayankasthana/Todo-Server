<?php

class GAuth {

    private static $user;

    public static function user($user = null) {
        if ($user != null)
            GAuth::$user = $user;
        //Log::info(print_r($user, true));
        return GAuth::$user;
    }

}
