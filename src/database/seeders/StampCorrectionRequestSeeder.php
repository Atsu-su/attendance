<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\RequestBreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class StampCorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startTime = ['08:30', '09:00', '09:30', '10:00', '10:30'];
        $endTime = ['17:30', '18:00', '18:30', '19:00', '19:30'];
        $breakStartTime = ['11:30' ,'12:40', '13:50'];
        $breakEndTime = ['12:30', '13:40', '14:50'];

        // 申請数
        $num = 5;

        for ($j = 1; $j <= User::count(); $j++) {
            $user = User::find($j);
            $attendances = Attendance::where('user_id', $user->id)
                ->limit($num)
                ->get();
            $date = Carbon::now();

            for ($i = 0; $i < $num; $i++) {
                $boolean = $i % 2 ? 0 : 1;
                $request = StampCorrectionRequest::create([
                    'attendance_id' => $attendances[$i]->id,
                    'user_id' => $user->id,
                    'is_approved' => $boolean,
                    'request_date' => $date->toDateString(),
                    'start_time' => Arr::random($startTime),
                    'end_time' => Arr::random($endTime),
                    'remarks' => 'Personal-'.$j.'-'.$i,
                ]);

                $hmax = ($i + 1) % 3 == 0 ? 3 : ($i + 1) % 3;
                for ($h = 0; $h < $hmax; $h++) {
                    RequestBreakTime::create([
                        'stamp_correction_request_id' => $request->id,
                        'start_time' => $breakStartTime[$h],
                        'end_time' => $breakEndTime[$h],
                    ]);
                }

                $date->addDay(5);
            }
        }
    }
}
