<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test01_01_ログアウト(): void
    {
        // 準備
        $user = User::factory()->create();

        // 実行
        $response = $this->actingAs($user)->get('/logout');

        // 検証
        $this->assertSame(302, $response->getStatusCode(), 'ログアウト後はリダイレクト');
        $response->assertRedirect('/login');
    }
}