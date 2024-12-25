<?php

namespace App\Http\Controllers;

use App\Models\RequestBreakTime;
use App\Models\StampCorrectionRequest;
use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        $user =  auth()->user();
        $requests =  StampCorrectionRequest::with('user')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($request) {
                $request->request_date = $request->dateFormatConvert($request->request_date);
                $request->date = $request->dateFormatConvert($request->date);
                return $request;
            });

        return view('stamp_correction_request_list', compact('requests'));
    }

    public function show($id)
    {
        $request = StampCorrectionRequest::with('attendance', 'user', 'requestBreakTimes')->find($id);
        $request->request_date = $request->toJapaneseDate($request->request_date);
        $request->attendance->date = $request->attendance->toJapaneseDate($request->attendance->date);
        $request->start_time = $request->timeFormatConvert($request->start_time);
        $request->end_time = $request->timeFormatConvert($request->end_time);

        $requestBreakTimes = $request
            ->requestBreakTimes
            ->map(function ($requestBreakTime) {
                $requestBreakTime->start_time = $requestBreakTime->timeFormatConvert($requestBreakTime->start_time);
                $requestBreakTime->end_time = $requestBreakTime->timeFormatConvert($requestBreakTime->end_time);
                return $requestBreakTime;
            });

        return view('stamp_correction_request_detail', compact('request', 'requestBreakTimes'));
    }

    // 管理者編
    //
    // 申請一覧表示
    // 申請詳細表示
    // 申請承認と結果画面表示
}
