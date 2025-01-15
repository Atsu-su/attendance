<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class EndWorkTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_退勤ボタン機能()
    {
        // Arrange
        $this->login();
        $keyword = '<button id="off-duty-button" class="c-btn c-btn--black c-btn--attendance-register">退勤</button>';
        $this->get('attendance');
        $this->post('attendance/startwork');

        // Act
        $response = $this->get('attendance');

        // Assert
        $response->assertOk()
            ->assertSee($keyword, false);

        // Act2
        $this->post('attendance/endwork');
        $response = $this->get('attendance');

        // Assert2
        $response->assertOk()
            ->assertSee('退勤済');
    }

    public function test_退勤時刻の確認()
    {
        // Arrange
        $user = $this->login();
        $now = now();
        $keyword = sprintf('<input class="input-time" type="text" name="end_time" value="%s" placeholder="18:00">',
            $now->format('H:i')
        );

        $this->get('/attendance');
        $this->post('/attendance/startwork');
        $this->post('/attendance/endwork');

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
