<?php

namespace App\Traits;

use App\Models\Attendance;

trait CheckTrait
{
    /**
     * 勤怠情報の所有者かどうかを確認
     * @param $id
     * @return Illuminate\Http\Response
     * @return bool
     * $id: attendancesテーブルのid
     */
    public function checkAttendanceOwner($id, $user)
    {
        $attendance = Attendance::where('id', $id)->first();
        if (!$attendance) {
            return abort(404);
        } else if ($attendance->user_id !== $user->id) {
            return abort(403);
        }
        return true;
    }
}
