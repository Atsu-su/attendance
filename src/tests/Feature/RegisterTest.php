<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stringable;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_名前未入力()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => '',
                'given_name' => 'とものり',
                'email' => 'safe@safe.com',
                'password' => 'abcdefghij',
                'confirm_password' => 'abcdefghij',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('姓名を入力してください');

        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => 'やました',
                'given_name' => '',
                'email' => 'safe@safe.com',
                'password' => 'abcdefghij',
                'confirm_password' => 'abcdefghij',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('姓名を入力してください');
    }

    public function test_メールアドレス未入力()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => 'やました',
                'given_name' => 'とものり',
                'email' => '',
                'password' => 'abcdefghij',
                'confirm_password' => 'abcdefghij',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワード7文字()
    {
        // Arrange
        $random = Str::random(7);

        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => 'やました',
                'given_name' => 'とものり',
                'email' => 'safe@safe.com',
                'password' => $random,
                'confirm_password' => $random,
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('パスワードは8文字以上で入力してください');
    }

    public function test_パスワード不一致()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => 'やました',
                'given_name' => 'とものり',
                'email' => 'safe@safe.com',
                'password' => 'abcdefghij',
                'confirm_password' => 'ABCDEFGHIJ',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('パスワードと一致しません');
    }

    public function test_パスワード未入力()
    {
        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => 'やました',
                'given_name' => 'とものり',
                'email' => 'safe@safe.com',
                'password' => '',
                'confirm_password' => 'abcdefghij',
            ]);

        // Assert
        $response->assertStatus(302)
            ->assertRedirect('/register');

        $this->followRedirects($response)
            ->assertSee('パスワードを入力してください');
    }

    public function test_登録成功()
    {
        // Arrange
        $data = [
            'family_name' => 'やました',
            'given_name' => 'とものり',
            'email' => 'safe@safe.com',
            'password' => 'abcdefghij',
        ];

        // Act
        $response = $this->from('/register')
            ->post('/register', [
                'family_name' => $data['family_name'],
                'given_name' => $data['given_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'confirm_password' => $data['password'],
            ]);

        // Assert
        // リダイレクトでOK
        $response->assertStatus(302);

        $user = User::where('email', $data['email'])->first();

        $this->assertEquals($data['family_name'], $user->family_name);
        $this->assertEquals($data['given_name'], $user->given_name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }
}
