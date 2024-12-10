<?php

namespace App\Http\Requests;

use App\Rules\AttendanceTimeRule;
use App\Rules\BreakEndRule;
use App\Rules\BreakStartRule;
use App\Rules\BreakTimeRule;
use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
                // new AttendanceTimeRule($this->attendance_start_time),
            ],
            'break_start_time' => [
                'required',
                'date_format:H:i',
                // new BreakStartRule($this->attendance_start_time),
            ],
            'break_end_time' => [
                'required',
                'date_format:H:i',
                new BreakEndRule($this->attendance_end_time),
                // new BreakTimeRule($this->break_start_time),
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
            'attendance_start_time.date_format' => '24時間表記（00:00）で入力してください',
            'attendance_end_time.required' => '退勤時間を入力してください',
            'attendance_end_time.date_format' => '24時間表記（00:00）で入力してください',
            'break_start_time.required' => '休憩開始時間を入力してください',
            'break_start_time.date_format' => '24時間表記（00:00）で入力してください',
            'break_end_time.required' => '休憩終了時間を入力してください',
            'break_end_time.date_format' => '24時間表記（00:00）で入力してください',
            'remarks.required' => '備考を入力してください',
            'remarks.string' => '文字列で入力してください',
            'remarks.max' => '100文字以内で入力してください',
        ];
    }
}
