<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class BreakTimeRule implements Rule
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
        // 休憩開始時間＜休憩終了時間ならtrue
        $breakStart = Carbon::createFromFormat('H:i', $this->startTime);
        $breakEnd = Carbon::createFromFormat('H:i', $value);
        return $breakStart->lessThan($breakEnd);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '休憩開始時間もしくは休憩終了時間が不適切な値です';
    }
}
