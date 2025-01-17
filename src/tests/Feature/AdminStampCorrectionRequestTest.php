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

class AdminStampCorrectionRequestTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_管理者勤怠詳細画面表示確認()
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

        $breakTime = BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '13:20',
            'end_time' => '14:50',
        ]);

        // Act
        $response = $this->get('attendance/'.$attendance->id);

        // Assert
        $response->assertOk()
            ->assertSeeInOrder([
                $user->family_name,
                $user->given_name,
                $attendance->start_time,
                $attendance->end_time,
                $breakTime->start_time,
                $breakTime->end_time,
            ]);
    }

    public function test_出勤時間＞退勤時間のケース()
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

        // $userrログアウト
        $this->post('logout');

        // 管理者ログイン
        $this->loginAsAdmin();

        // Act
        $response = $this->from('admin/attendance/'.$attendance->id)
            ->post('admin/stamp_correction_request/'.$attendance->id, [
                'start_time' => '12:00',
                'end_time' => '09:00',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('admin/attendance/'.$attendance->id);

        $this->get('/attendance/'.$attendance->id)
            ->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    // -----------------------------------------------------
    // 実施不可のためコメントアウト
    // -----------------------------------------------------
    // public function test_休憩開始時間＞退勤時間のケース()
    // {
    //     // Arrange
    //     $user = $this->login();
    //     $now = now();

    //     $attendance = Attendance::create([
    //         'user_id' => $user->id,
    //         'date' => $now->copy()->subDay(1)->format('Y-m-d'),
    //         'status' => Attendance::OFF_DUTY[0],
    //         'start_time' => '09:00',
    //         'end_time' => '18:00',
    //     ]);

    //     BreakTime::create([
    //         'attendance_id' => $attendance->id,
    //         'start_time' => '13:20',
    //         'end_time' => '14:50',
    //     ]);

    //     // Act
    //     $response = $this->from('attendance/'.$attendance->id)
    //         ->post('stamp_correction_request/'.$attendance->id, [
    //             'end_time' => '17:00',
    //             'break_start_time' => ['18:00'],
    //             'break_end_time' => ['19:00'],
    //         ]);

    //     // Assert
    //     $response->assertStatus(302)
    //         ->assertRedirect('/attendance/'.$attendance->id);

    //     $this->get('/attendance/'.$attendance->id)
    //         ->assertSee('休憩開始時間が勤務時間外です');
    // }

    public function test_休憩終了時間＞退勤時間のケース()
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

        // $userrログアウト
        $this->post('logout');

        // 管理者ログイン
        $this->loginAsAdmin();

        // Act
        $response = $this->from('admin/attendance/'.$attendance->id)
            ->post('admin/stamp_correction_request/'.$attendance->id, [
                'end_time' => '15:00',
                'break_start_time' => ['14:20'],
                'break_end_time' => ['16:00'],
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('admin/attendance/'.$attendance->id);

        $this->get('admin/attendance/'.$attendance->id)
            ->assertSee('休憩終了時間が勤務時間外です');
    }

    public function test_備考欄未入力のケース()
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

        // $userrログアウト
        $this->post('logout');

        // 管理者ログイン
        $this->loginAsAdmin();

        // Act
        $response = $this->from('admin/attendance/'.$attendance->id)
            ->post('admin/stamp_correction_request/'.$attendance->id, [
                'remarks' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('admin/attendance/'.$attendance->id);

        $this->get('admin/attendance/'.$attendance->id)
            ->assertSee('備考を入力してください');
    }
}
