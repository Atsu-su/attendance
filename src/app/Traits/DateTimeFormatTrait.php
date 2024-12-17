<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateTimeFormatTrait
{
    public function isValidTF($time) //TF = TimeFormat
    {
        return preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time);
    }

    public function dateFormatConvert($date)
    {
        return date('Y/m/d', strtotime($date));
    }

    public function timeFormatConvert($time)
    {
        return date('H:i', strtotime($time));
    }

    public function diffTime($time1, $time2)
    {
        $diff = Carbon::parse($time1)->diff(Carbon::parse($time2));
        return $diff->format('%H:%I');
    }
}
