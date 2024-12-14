<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * スタッフ一覧表示
     * @param int $id
     * $id: attendancesテーブルのid
     */
     public function showStaffList()
     {
        $users = User::where('is_active', true)->get();
        return view('admin_staff_list', compact('users'));
     }
}
