<?php

namespace Tests;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function login($user = null)
    {
        $user ??= User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function loginAsAdmin($admin = null)
    {
        $admin ??= Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $admin;
    }
}
