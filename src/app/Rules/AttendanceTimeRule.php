<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class AttendanceTimeRule implements Rule
{
    private $startTime;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 勤務開始時間＜＝勤務終了時間ならtrue
        $start = Carbon::createFromFormat('H:i', $this->startTime);
        $end = Carbon::createFromFormat('H:i', $value);
        return $start->lessThanOrEqualTo($end);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '出勤時間もしくは退勤時間が不適切な値です';
    }
}
