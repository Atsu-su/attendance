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

class StampCorrectionRequestTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
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

        // Act
        $response = $this->from('attendance/'.$attendance->id)
            ->post('stamp_correction_request/'.$attendance->id, [
                'start_time' => '12:00',
                'end_time' => '09:00',
            ]);

        // Assert
        $response->assertStatus(302)
            // ->assertRedirect('/attendance/'.$attendance->id);
            ->assertRedirect('/attendance/'.$attendance->id);

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

        // Act
        $response = $this->from('attendance/'.$attendance->id)
            ->post('stamp_correction_request/'.$attendance->id, [
                'end_time' => '15:00',
                'break_start_time' => ['14:20'],
                'break_end_time' => ['16:00'],
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/attendance/'.$attendance->id);

        $this->get('/attendance/'.$attendance->id)
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

        // Act
        $response = $this->from('attendance/'.$attendance->id)
            ->post('stamp_correction_request/'.$attendance->id, [
                'remarks' => '',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/attendance/'.$attendance->id);

        $this->get('/attendance/'.$attendance->id)
            ->assertSee('備考を入力してください');
    }

    public function test_管理者での申請表示確認()
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

        // 修正申請
        $this->from('attendance/'.$attendance->id)
            ->post('stamp_correction_request/'.$attendance->id, [
                'start_time' => '10:00',
                'end_time' => '17:00',
                'break_start_time' => ['13:00'],
                'break_end_time' => ['14:00'],
                'remarks' => '申請理由です',
            ]);

        $request = StampCorrectionRequest::where('attendance_id', $attendance->id)
            ->first();

        $this->loginAsAdmin();

        // Act1
        $response1 = $this->get('admin/stamp_correction_request/approve/'.$request->id);

        // Assert1
        $response1->assertOk();

        // Act2
        $response2 = $this->get('admin/stamp_correction_request/list');

        // Assert2
        $response2->assertOk()
            ->assertSeeInOrder([
                '<div id="first-tab" class="tab first-tab">',
                $user->family_name,
                $user->given_name,
            ], false);
    }

    public function test_ユーザでの申請表示確認()
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

        // 修正申請
        $this->from('attendance/'.$attendance->id)
            ->post('stamp_correction_request/'.$attendance->id, [
                'start_time' => '10:00',
                'end_time' => '17:00',
                'break_start_time' => ['13:00'],
                'break_end_time' => ['14:00'],
                'remarks' => '申請理由です',
            ]);

        // Act
        $response = $this->get('stamp_correction_request/list');

        // Assert
        $response->assertOk()
            ->assertSeeInOrder([
                '<div id="first-tab" class="tab first-tab">',
                $user->family_name,
                $user->given_name,
            ], false);
    }

    public function test_承認済み申請の表示確認()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $now = now();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $now->copy()->subDay(1)->format('Y-m-d'),
            'status' => Attendance::OFF_DUTY[0],
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $now->copy()->subDay(1)->format('Y-m-d'),
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

        // 修正申請
        $this->post('stamp_correction_request/'.$attendance1->id, [
            'start_time' => '10:00',
            'end_time' => '17:00',
            'break_start_time' => ['13:00'],
            'break_end_time' => ['14:00'],
            'remarks' => '申請理由その1です',
        ]);

        $this->post('stamp_correction_request/'.$attendance2->id, [
            'start_time' => '11:00',
            'end_time' => '21:00',
            'break_start_time' => ['15:00'],
            'break_end_time' => ['17:00'],
            'remarks' => '申請理由その2です',
        ]);

        $request = StampCorrectionRequest::where('attendance_id', $attendance1->id)
            ->first();

        // 承認
        $this->post('admin/stamp_correction_request/approve/'.$request->id);

        // Act
        $response = $this->get('stamp_correction_request/list');

        // Assert
        $response->assertOk()
            ->assertSeeInOrder([
                '<div id="second-tab" class="tab second-tab js-hidden">',
                $user1->family_name,
                $user1->given_name,
            ], false);
    }

    public function test_ユーザでの申請詳細表示確認()
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

        // 修正申請
        $this->from('attendance/'.$attendance->id)
            ->post('stamp_correction_request/'.$attendance->id, [
                'start_time' => '10:00',
                'end_time' => '17:00',
                'break_start_time' => ['13:00'],
                'break_end_time' => ['14:00'],
                'remarks' => '申請理由です',
            ]);

        $request = StampCorrectionRequest::where('attendance_id', $attendance->id)
            ->first();

        // Act
        $response = $this->get('stamp_correction_request/'.$request->id);

        // Assert
        $response->assertOk();
    }
}
