<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class StartWorkTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_出勤ボタン機能()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="on-duty-button" class="c-btn c-btn--black c-btn--attendance-register ">出勤</button>';

        // Act
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee($keyword, false);

        // Act2
        $this->post('attendance/startwork');
        $response = $this->get('/attendance');

        // Assert2
        $response->assertOk()
            ->assertSee('勤務中');
    }

    public function test_出勤処理の複数回実行不可確認()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="on-duty-button" class="c-btn c-btn--black c-btn--attendance-register ">出勤</button>';
        $this->get('attendance/');
        $this->post('attendance/startwork');
        $this->post('attendance/endwork');

        // Act
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertDontSee($keyword, false);
    }

    public function test_出勤時刻の管理画面からの確認()
    {
        // Arrange
        $now = now();
        $user = $this->login();
        $this->get('attendance/');
        $this->post('attendance/startwork');
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $now->format('Y-m-d'))
            ->first();

        // Act
        $this->loginAsAdmin();
        $response = $this->get(sprintf(
            'admin/attendance/list/%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d'))
        );

        // Assert
        $response->assertOk()
            ->assertSee($attendance->timeFormatConvert($attendance->start_time));
    }
}
