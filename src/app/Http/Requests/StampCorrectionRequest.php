<?php

namespace App\Http\Requests;

use App\Rules\AttendanceTimeRule;
use App\Rules\BreakEndRule;
use App\Rules\BreakStartRule;
use App\Rules\BreakTimeRule;
use App\Traits\DateTimeFormatTrait;
use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequest extends FormRequest
{
    use DateTimeFormatTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $breakStartTimes = $this->input('break_start_time', []);
        $breakEndTimes = $this->input('break_end_time', []);

        $formattedStartTimes = array_map(function ($time) {
            return $this->timeToTwoDigits($time);
        }, $breakStartTimes);

        $formattedEndTimes = array_map(function ($time) {
            return $this->timeToTwoDigits($time);
        }, $breakEndTimes);

        $this->merge([
            'start_time' => $this->timeToTwoDigits($this->start_time),
            'end_time' => $this->timeToTwoDigits($this->end_time),
            'break_start_time' => $formattedStartTimes,
            'break_end_time' => $formattedEndTimes,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => [
                'required',
                'date_format:H:i'
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                function($attribute, $value, $fail) {
                    if ($this->isValidTF($this->start_time) && $this->isValidTF($this->end_time)) {
                        $rule = new AttendanceTimeRule($this->start_time);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                },
            ],
            'break_start_time.*' => [
                'required',
                'date_format:H:i',
                function($attribute, $value, $fail) {
                    if ($this->isValidTF($this->start_time) && $this->isValidTF($value)) {
                        $rule = new BreakStartRule($this->start_time);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                },
            ],
            'break_end_time.*' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if ($this->isValidTF($this->end_time) && $this->isValidTF($value)) {
                        $rule = new BreakEndRule($this->end_time);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                },
                function ($attribute, $value, $fail) {
                    // 比較対象のbreak_start_timeが配列なのでindexを取得
                    preg_match('/break_end_time\.(\d+)/', $attribute, $matches);
                    $index = $matches[1];
                    $breakStartTime = $this->break_start_time[$index];

                    if ($this->isValidTF($breakStartTime) && $this->isValidTF($value)) {
                        $rule = new BreakTimeRule($breakStartTime);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                }
            ],
            'remarks' => [
                'required',
                'string',
                'max:100'
            ]
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'start_time.date_format' => '出勤時間は24時間表記（00:00）で入力してください',
            'end_time.required' => '退勤時間を入力してください',
            'end_time.date_format' => '退勤時間は24時間表記（00:00）で入力してください',
            'break_start_time.*.required' => '休憩開始時間を入力してください',
            'break_start_time.*.date_format' => '休憩開始時間は24時間表記（00:00）で入力してください',
            'break_end_time.*.required' => '休憩終了時間を入力してください',
            'break_end_time.*.date_format' => '休憩終了時間は24時間表記（00:00）で入力してください',
            'remarks.required' => '備考を入力してください',
            'remarks.string' => '文字列で入力してください',
            'remarks.max' => '100文字以内で入力してください',
        ];
    }
}
