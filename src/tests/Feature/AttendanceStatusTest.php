<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class AttendanceStatusTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_勤務外表示()
    {
        // Arrange
        $this->login();

        // Act
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee('勤務外');
    }

    public function test_勤務中表示()
    {
        // Arrange
        $this->login();
        $this->get('attendance/');  // 一度アクセスする必要がある

        // Act
        $this->post('attendance/startwork');
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee('勤務中');
    }

    public function test_休憩中表示()
    {
        // Arrange
        $this->login();
        $this->get('attendance/');  // 一度アクセスする必要がある

        // Act
        $this->post('attendance/startwork');
        $this->post('attendance/startbreak');
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee('休憩中');
    }

    public function test_退勤済表示()
    {
        // Arrange
        $this->login();
        $this->get('attendance/');  // 一度アクセスする必要がある

        // Act
        $this->post('attendance/startwork');
        $this->post('attendance/endwork');
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee('退勤済');
    }
}
