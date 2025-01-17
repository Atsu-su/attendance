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

class AttendanceDetailTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_勤怠詳細名前表示()
    {
        // Arrange
        $user = $this->login();
        $now = now();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $now->copy()->subDay(1)->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '13:20',
            'end_time' => '14:50',
        ]);

        // Act
        $response = $this->get('attendance/'.$attendance->id);

        // Assert
        $response->assertOk()
            ->assertSee($user->family_name)
            ->assertSee($user->given_name);
    }

    public function test_勤怠詳細日付表示()
    {
        // Arrange
        $user = $this->login();
        $now = now();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $now->copy()->subDay(1)->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '13:20',
            'end_time' => '14:50',
        ]);

        // Act
        $response = $this->get('attendance/'.$attendance->id);

        // Assert
        $response->assertOk()
            ->assertSee($now->copy()->subDay(1)->isoFormat('YYYY年MM月DD日'));
    }
}
