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
            ->get()
            ->map(function ($request) {
                $request->request_date = $request->dateFormatConvert($request->request_date);
                $request->date = $request->dateFormatConvert($request->date);
                return $request;
            });

        return view('stamp_correction_request_list', compact('requests'));
    }

    // 管理者編
    //
    // 申請一覧表示
    // 申請詳細表示
    // 申請承認と結果画面表示
}
