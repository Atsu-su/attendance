<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class BreakTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_休憩入ボタン機能()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="break-start-button" class="c-btn c-btn--white c-btn--attendance-register">休憩入</button>';
        $this->get('/attendance');
        $this->post('/attendance/startwork');

        // Act1
        $response = $this->get('/attendance');

        // Assert1
        $response->assertOk()
            ->assertSee($keyword, false);

        // Arrange2
        $this->post('/attendance/startbreak');

        // Act2
        $response = $this->get('/attendance');

        // Assert2
        $response->assertOk()
            ->assertSee('休憩中');
    }

    public function test_休憩処理の複数回実行可能を確認()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="break-start-button" class="c-btn c-btn--white c-btn--attendance-register">休憩入</button>';
        $this->post('/attendance/startwork');
        $this->post('/attendance/startbreak');
        $this->post('/attendance/endbreak');

        // Act
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee($keyword, false);
    }

    public function test_休憩戻ボタン機能()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="break-end-button" class="c-btn c-btn--white c-btn--attendance-register buttons-back-to-work ">休憩戻</button>';
        $this->get('/attendance');
        $this->post('/attendance/startwork');
        $this->post('/attendance/startbreak');

        // Act1
        $response = $this->get('/attendance');

        // Assert1
        $response->assertOk()
            ->assertSee($keyword, false);

        // Arrange2
        $this->post('/attendance/endbreak');

        // Act2
        $response = $this->get('/attendance');

        // Assert2
        $response->assertOk()
            ->assertSee('勤務中');
    }

    public function test_休憩処理の複数回実行可能を確認2()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="break-end-button" class="c-btn c-btn--white c-btn--attendance-register buttons-back-to-work ">休憩戻</button>';
        $this->get('/attendance');
        $this->post('/attendance/startwork');
        $this->post('/attendance/startbreak');
        $this->post('/attendance/endbreak');
        $this->post('/attendance/startbreak');

        // Act
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee($keyword, false);
    }

    public function test_休憩時刻の確認()
    {
        // Arrange
        $user = $this->login();
        $now = now();
        $keyword = sprintf('<input class="input-time" type="text" name="break_start_time[]" value="%s" placeholder="12:00"><span class="wave">～</span><input class="input-time" type="text" name="break_end_time[]" value="%s" placeholder="13:00">',
            $now->format('H:i'), $now->format('H:i')
        );

        $this->get('/attendance');
        $this->post('/attendance/startwork');
        $this->post('/attendance/startbreak');
        $this->post('/attendance/endbreak');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $now->format('Y-m-d'))
            ->first();

        // 当日は申請できない仕様のため1日ずらす
        $attendance->update([
            'date' => $now->copy()->subDay()->format('Y-m-d')
        ]);

        // Act
        $this->loginAsAdmin();
        $response = $this->get(sprintf('admin/attendance/%s', $attendance->id));

        // Assert
        $response->assertOk()
            ->assertSee($keyword, false)
            ->assertSee($now->copy()->subDay()->isoFormat('YYYY年MM月DD日'));
    }
}
