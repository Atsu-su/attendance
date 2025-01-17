<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class DailyAttendanceListTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_承認済み申請の表示確認()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $now = now();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $now->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $now->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '10:30',
            'end_time' => '19:20',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'start_time' => '13:20',
            'end_time' => '14:50',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'start_time' => '15:30',
            'end_time' => '16:50',
        ]);

        // 管理者のログイン
        $this->loginAsAdmin();

        // Act
        $response = $this->get(sprintf('admin/attendance/list/%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        ));

        // Assert
        $response->assertOk()
            ->assertSeeInOrder([
                $user1->family_name,
                $user1->given_name,
            ], false)
            ->assertSeeInOrder([
                $user2->family_name,
                $user2->given_name,
            ], false);
    }

    public function test_当日日付の表示確認()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $now = now();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $now->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $now->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '10:30',
            'end_time' => '19:20',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'start_time' => '13:20',
            'end_time' => '14:50',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'start_time' => '15:30',
            'end_time' => '16:50',
        ]);

        // 管理者のログイン
        $this->loginAsAdmin();

        // Act
        $response = $this->get(sprintf('admin/attendance/list/%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        ));

        // Assert
        $response->assertOk()
            ->assertSee($now->isoFormat('YYYY年MM月DD日'));
    }

    public function test_前日の表示確認()
    {
        // 管理者のログイン
        $this->loginAsAdmin();
        $now = now();
        $preDay = $now->copy()->subDay();

        // Act
        $response = $this->get(sprintf('admin/attendance/list/%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        ));

        // Assert
        $response->assertOk()
            ->assertSee(sprintf('admin/attendance/list/%s/%s/%s',
                $preDay->format('Y'),
                $preDay->format('m'),
                $preDay->format('d')
            ));
    }

    public function test_翌日の表示確認()
    {
        // 管理者のログイン
        $this->loginAsAdmin();
        $now = now();
        $preDay = $now->copy()->subDay();

        // Act
        $response = $this->get(sprintf('admin/attendance/list/%s/%s/%s',
            $preDay->format('Y'),
            $preDay->format('m'),
            $preDay->format('d')
        ));

        // Assert
        $response->assertOk()
            ->assertSee(sprintf('admin/attendance/list/%s/%s/%s',
                $now->format('Y'),
                $now->format('m'),
                $now->format('d')
        ));
    }
}
