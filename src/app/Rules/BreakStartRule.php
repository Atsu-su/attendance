<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class BreakStartRule implements Rule
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
        // 勤務開始時間＜＝休憩開始時間ならtrue
        $start = Carbon::createFromFormat('H:i', $this->startTime);
        $breakStart = Carbon::createFromFormat('H:i', $value);
        return $start->lessThanOrEqualTo($breakStart);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '休憩開始時間が勤務時間外です';
    }
}
