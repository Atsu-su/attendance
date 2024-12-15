<?php

namespace App\Http\Controllers;

use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use App\Traits\DateTimeFormatTrait;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use DateTimeFormatTrait;

    public function edit($id)
    {
        $attendance = Attendance::with('user')->find($id);

        $attendance->date = $this->dateFormatConvert($attendance->date);
        $attendance->start_time = $this->timeFormatConvert($attendance->start_time);
        $attendance->end_time = $this->timeFormatConvert($attendance->end_time);

        // break_timeを追加
        $attendance->break_time = $this->diffTime($attendance->break_start_time, $attendance->break_end_time);

        return view('attendance_detail', compact('attendance'));
    }

    public function store(StampCorrectionRequest $request)
    {
        // return redirect()->route('attendance.edit', ['id' => $attendance->id]);
    }
}
