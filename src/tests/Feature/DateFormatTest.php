<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class DateFormatTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_日時取得()
    {
        // Arrange
        $this->login();
        $now = now()->subHours(8);

        // Act
        $response = $this->get('/attendance');

        // Assert
        $response->assertOk()
            ->assertSee($now->isoFormat('YYYY年MM月DD日(ddd)'))
            ->assertSee($now->format('H:i'));
    }
}
