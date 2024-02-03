<?php

namespace Tests\Feature\Logout;

use Tests\TestCase;
use App\Services\TubuyakiUser;
use App\Repositories\User\UserRepository;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test01_01_ログアウト(): void
    {
        // 準備
        $user = TubuyakiUser::create(
            app()->make(UserRepository::class),
            account_name: 'test_user',
            name: '検証太郎',
            email: 'test@example.com',
            password: '1111aaaa',
            remember_token: 'xxxxyyyy',
        );

        // 実行
        $response = $this->actingAs($user)->post('/logout');

        // 検証
        $this->assertSame(302, $response->getStatusCode(), 'ログアウト後はリダイレクト');
        $response->assertRedirect('/login');
    }
}