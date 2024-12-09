<?php

namespace Database\Seeders;

use App\Models\StampCorrectionRequest;
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
        $breakTime = ['00:30', '01:00', '01:30', '02:00', '02:30'];

        for ($j = 1; $j <= User::count(); $j++) {
            $user = User::find($j);
            $date = Carbon::now();

            for ($i = 0; $i < 5; $i++) {
                StampCorrectionRequest::create([
                    'user_id' => $user->id,
                    'is_approved' => 0,
                    'request_date' => $date->toDateString(),
                    'date' => $date->addDays(-2)->toDateString(),
                    'start_time' => Arr::random($startTime),
                    'end_time' => Arr::random($endTime),
                    'break_time' => Arr::random($breakTime),
                    'reason' => 'Personal-'.$j.'-'.$i,
                ]);

                $date->addDay(5);
            }
        }
    }
}
