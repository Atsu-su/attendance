<?php

namespace Database\Seeders;

use App\Models\Attendance;
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
        $startTime = ['08:30', '09:00', '09:30', '10:00', '10:30'];
        $endTime = ['17:30', '18:00', '18:30', '19:00', '19:30'];
        $breakStartTime = ['11:30' ,'11:40', '11:50', '12:00', '12:10'];
        $breakEndTime = ['12:30', '12:40', '12:50', '13:00', '13:10'];

        for ($j = 1; $j <= User::count(); $j++) {
            $user = User::find($j);
            $date = Carbon::now();

            for ($i = 0; $i < 60; $i++) {
                Attendance::create([
                    'user_id' => $user->id,
                    'status' => 'off_duty',
                    'date' => $date->toDateString(),
                    'start_time' => Arr::random($startTime),
                    'end_time' => Arr::random($endTime),
                    'break_start_time' => Arr::random($breakStartTime),
                    'break_end_time' => Arr::random($breakEndTime),
                ]);

                $date->addDay();
            }

            Attendance::create([
                'user_id' => $user->id,
                'status' => 'on_duty',
                'date' => $date->toDateString(),
                'start_time' => Arr::random($startTime),
                'end_time' => null,
                'break_start_time' => null,
                'break_end_time' => null,
            ]);
        }
    }
}
