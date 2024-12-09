<?php

namespace App\Http\Requests;

use App\Rules\AttendanceTimeRule;
use App\Rules\BreakEndRule;
use App\Rules\BreakStartRule;
use App\Rules\BreakTimeRule;
use App\Traits\TimeFormatTrait;
use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequest extends FormRequest
{
    use TimeFormatTrait;

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
        $this->merge([
            'attendance_start_time' => $this->formatTime($this->attendance_start_time),
            'attendance_end_time' => $this->formatTime($this->attendance_end_time),
            'break_start_time' => $this->formatTime($this->break_start_time),
            'break_end_time' => $this->formatTime($this->break_end_time)
        ]);
    }

    private function formatTime($time)
    {
        // 時間が1桁で入力された場合、2桁に変換する
        if (preg_match('/^(0?[0-9]|1[0-9]|2[0-3]):([0-5]?[0-9])$/', $time, $matches)) {
            return sprintf('%02d:%02d', $matches[1], $matches[2]);
        }
        return $time;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => [
                'required',
                'date'
            ],
            'attendance_start_time' => [
                'required',
                'date_format:H:i'
            ],
            'attendance_end_time' => [
                'required',
                'date_format:H:i',
                function($attribute, $value, $fail) {
                    if ($this->isValidTF($this->attendance_start_time) && $this->isValidTF($this->attendance_end_time)) {
                        $rule = new AttendanceTimeRule($this->attendance_start_time);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                },
            ],
            'break_start_time' => [
                'required',
                'date_format:H:i',
                function($attribute, $value, $fail) {
                    if ($this->isValidTF($this->attendance_start_time) && $this->isValidTF($this->break_start_time)) {
                        $rule = new BreakStartRule($this->attendance_start_time);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                },
            ],
            'break_end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if ($this->isValidTF($this->attendance_end_time) && $this->isValidTF($this->break_end_time)) {
                        $rule = new BreakEndRule($this->attendance_end_time);
                        if (!$rule->passes($attribute, $value)) {
                            $fail($rule->message());
                        }
                    }
                },
                function ($attribute, $value, $fail) {
                    if ($this->isValidTF($this->break_start_time) && $this->isValidTF($this->break_end_time)) {
                        $rule = new BreakTimeRule($this->break_start_time);
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
            'date.date' => '日付を入力してください',
            'date.required' => '日付を入力してください',
            'attendance_start_time.required' => '出勤時間を入力してください',
            'attendance_start_time.date_format' => '出勤時間は24時間表記（00:00）で入力してください',
            'attendance_end_time.required' => '退勤時間を入力してください',
            'attendance_end_time.date_format' => '退勤時間は24時間表記（00:00）で入力してください',
            'break_start_time.required' => '休憩開始時間を入力してください',
            'break_start_time.date_format' => '休憩開始時間は24時間表記（00:00）で入力してください',
            'break_end_time.required' => '休憩終了時間を入力してください',
            'break_end_time.date_format' => '休憩終了時間は24時間表記（00:00）で入力してください',
            'remarks.required' => '備考を入力してください',
            'remarks.string' => '文字列で入力してください',
            'remarks.max' => '100文字以内で入力してください',
        ];
    }
}
