<?php

namespace App\Rules;

use App\Traits\TimeFormatTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class BreakEndRule implements Rule
{
    use TimeFormatTrait;

    private $endTime;
    private $endTimeFormat = true;
    private $breakEndTimeFormat = true;

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
        if (!$this->isValidTimeFormat($value)) {
            $this->breakEndTimeFormat = false;
            return false;
        } elseif (!$this->isValidTimeFormat($this->endTime)) {
            $this->endTimeFormat = false;
            return false;
        }

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
        if ($this->breakEndTimeFormat === false) {
            return '休憩終了時間が不適切な値です';
        } else if ($this->endTimeFormat === false) {
            return '退勤時間が不適切な値です';
        }

        return '休憩時間（休憩終了時間）が勤務時間外です';
    }
}
