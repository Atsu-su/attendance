<?php

namespace App\Traits;

trait TimeFormatTrait
{
    protected function isValidTF($time) //TF = TimeFormat
    {
        return preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time);
    }
}
