<?php

namespace Tests\Feature\User;

use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test01_01_ユーザー登録(): void
    {
        $response = $this->post('/api/user', [
            'account_name' => 'newUser',
            'name' => '新規追加',
            'email' => 'test@example.com',
            'password' => 'aabbcc123',
        ]);
        $response->assertStatus(201);
    }
}