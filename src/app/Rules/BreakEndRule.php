<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class BreakEndRule implements Rule
{
    private $endTime;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($endTime)
    {
        $this->endTime = $endTime;
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
        // 休憩終了時間＜勤務終了時間ならtrue
        $end = Carbon::createFromFormat('H:i', $this->endTime);
        $breakEnd = Carbon::createFromFormat('H:i', $value);
        return $breakEnd->lessThanOrEqualTo($end);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '休憩終了時間が勤務時間外です';
    }
}
