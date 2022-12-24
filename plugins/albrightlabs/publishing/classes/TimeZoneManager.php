<?php namespace Albrightlabs\Publishing\Classes;

use BackendAuth;
use Backend\Models\UserPreference;

class TimeZoneManager
{

    /**
     * Returns admin timezone offset
     */
    public static function onGetAdminTimezoneOffset()
    {
        // set a reasonable default
        $hoursoffset = 0;

        // get admin preference timezone
        if($preferences = UserPreference::where('user_id', BackendAuth::getUser()->id)->where('group', 'backend')->where('item', 'preferences')->first()){
            $timezone = $preferences->value['timezone'];
        }
        // or default to ET
        else{
            $timezone = 'America/New_York';
        }

        // set local timezone
        date_default_timezone_set($timezone);

        // check if DST or NOT DST
        if (date('I', time())) {
            // DST
            $hoursoffset = date('Z');
        } else {
            // NOT DST
            $hoursoffset = date('Z')-3600;
        }

        // return offset from UTC in seconds
        return $hoursoffset;
    }

}
