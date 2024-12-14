<?php

namespace App\Http\Controllers;

use App\Models\StampCorrectionRequest;
use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        $user =  auth()->user();
        $requests =  StampCorrectionRequest::with('user')
            ->where('user_id', $user->id)
            ->get();

        // date/timeのフォーマットを変更
        foreach ($requests as $request) {
            $request->request_date = date('Y/m/d', strtotime($request->request_date));
            $request->date = date('Y/m/d', strtotime($request->date));
            $request->start_time = date('H:i', strtotime($request->start_time));
            $request->end_time = date('H:i', strtotime($request->end_time));
            $request->break_time = date('H:i', strtotime($request->break_time));
        }

        return view('stamp_correction_request_list', compact('requests'));
    }

    public function edit()
    {
        //
    }

    // 管理者編
    //
    // 申請一覧表示
    // 申請詳細表示
    // 申請承認
}
