<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    public function minutesFormatConvert($mins)
    {
        if ($mins === null) return null;

        $hours = floor($mins / 60);
        $minutes = $mins % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_勤怠情報一覧表示()
    {
        // Arrange
        $now = now();
        $user = $this->login();

        $attendanceData = [
            [
                'attendance' => [
                    'user_id' => $user->id,
                    'date' => $now->copy()->subDay(1)->format('Y-m-d'),
                    'status' => Attendance::OFF_DUTY[0],
                    'start_time' => '09:00',
                    'end_time' => '18:00',
                ],
                'break' => [
                    'start_time' => '13:20',
                    'end_time' => '14:50',
                ],
            ],
            [
                'attendance' => [
                    'user_id' => $user->id,
                    'date' => $now->copy()->subDay(2)->format('Y-m-d'),
                    'status' => Attendance::OFF_DUTY[0],
                    'start_time' => '08:00',
                    'end_time' => '17:00',
                ],
                'break' => [
                    'start_time' => '12:00',
                    'end_time' => '13:00',
                ],
            ],
            [
                'attendance' => [
                    'user_id' => $user->id,
                    'date' => $now->copy()->subDay(3)->format('Y-m-d'),
                    'status' => Attendance::OFF_DUTY[0],
                    'start_time' => '07:00',
                    'end_time' => '16:00',
                ],
                'break' => [
                    'start_time' => '11:00',
                    'end_time' => '12:00',
                ],
            ]
        ];

        foreach ($attendanceData as $data) {
            $attendance = Attendance::create($data['attendance']);
            $data['break']['attendance_id'] = $attendance->id;
            BreakTime::create($data['break']);
        }

        // Act
        $response = $this->get(sprintf('attendance/list/%s/%s',
            $now->format('Y'),
            $now->format('m'),
        ));

        // Assert
        // $response->assertOk();

        foreach ($attendanceData as $data) {
            $startTime = Carbon::parse($data['attendance']['start_time']);
            $endTime = Carbon::parse($data['attendance']['end_time']);
            $breakStartTime = Carbon::parse($data['break']['start_time']);
            $breakEndTime = Carbon::parse($data['break']['end_time']);

            $diffBreakTimeInMinutes = $breakStartTime->diffInMinutes($breakEndTime);
            $diffWorkTimeInMinutes = $startTime->diffInMinutes($endTime);
            $totalWorkTimeInMinutes = $diffWorkTimeInMinutes - $diffBreakTimeInMinutes;

            $breakTime = $this->minutesFormatConvert($diffBreakTimeInMinutes);
            $totalWorkTime = $this->minutesFormatConvert($totalWorkTimeInMinutes);

            $response->assertSeeInOrder([
                Carbon::parse($data['attendance']['date'])->isoFormat('MM月DD日'),
                $data['attendance']['start_time'],
                $data['attendance']['end_time'],
                $breakTime,
                $totalWorkTime
            ]);
        }
    }

    public function test_当月の情報が表示されるか確認()
    {
        // Arrange
        $this->login();
        $now = now();

        // Act
        $response = $this->get(sprintf('attendance/list/%s/%s',
            $now->format('Y'),
            $now->format('m'),
        ));

        // Assert
        $response->assertOk()
            ->assertSee($now->isoFormat('YYYY年MM月'));
    }

    public function test_前月のリンク存在確認()
    {
        // Arrange
        $this->login();
        $now = now();

        // Act
        $response = $this->get(sprintf('attendance/list/%s/%s',
            $now->format('Y'),
            $now->format('m'),
        ));

        // Assert
        $response->assertOk()
            ->assertSee(sprintf('attendance/list/%s/%s',
                $now->copy()->subMonth()->format('Y'),
                $now->copy()->subMonth()->format('m'),
            ));
    }

    public function test_翌月のリンク存在確認()
    {
        // Arrange
        $this->login();
        $now = now();

        // Act
        $response = $this->get(sprintf('attendance/list/%s/%s',
            $now->copy()->subMonth()->format('Y'),
            $now->copy()->subMonth()->format('m'),
        ));

        // Assert
        $response->assertOk()
            ->assertSee(sprintf('attendance/list/%s/%s',
                $now->format('Y'),
                $now->format('m'),
            ));
    }

    public function test_詳細表示リンク存在確認()
    {
        // Arrange
        $user = $this->login();
        $now = now();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $now->copy()->subDay()->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        // Act
        $response = $this->get(sprintf('attendance/list/%s/%s',
            $now->format('Y'),
            $now->format('m'),
        ));

        // Assert
        $response->assertOk()
            ->assertSee('attendance/'.$attendance->id);
    }
}
