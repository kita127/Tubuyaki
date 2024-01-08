<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test01_01_メールアドレスとパスが一致したらリダイレクト(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password',
        ]);

        $this->assertSame(302, $response->getStatusCode(), '認証成功後はリダイレクト');
    }
}
