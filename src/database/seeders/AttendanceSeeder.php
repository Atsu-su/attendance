<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $days = 60;
        $startTime = ['08:30', '09:00', '09:30', '10:00', '10:30'];
        $endTime = ['17:30', '18:00', '18:30', '19:00', '19:30'];
        $breakStartTime = ['11:19:23' ,'12:10:49', '13:34:21'];
        $breakEndTime = ['12:23:10', '13:28:10', '14:50:50'];

        for ($j = 1; $j <= User::count(); $j++) {
            $user = User::find($j);
            $date = Carbon::now()->addDays(- $days - 1);    // 1つon_dutyで作成するため

            for ($i = 0; $i < $days; $i++) {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'status' => 'off_duty',
                    'date' => $date->toDateString(),
                    'start_time' => Arr::random($startTime),
                    'end_time' => Arr::random($endTime),
                ]);

                $hmax = ($i + 1) % 3 == 0 ? 3 : ($i + 1) % 3;
                for ($h = 0; $h < $hmax; $h++) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => $breakStartTime[$h],
                        'end_time' => $breakEndTime[$h],
                    ]);
                }

                $date->addDay();
            }

            Attendance::create([
                'user_id' => $user->id,
                'status' => 'on_duty',
                'date' => $date->toDateString(),
                'start_time' => Arr::random($startTime),
                'end_time' => null,
            ]);
        }
    }
}
