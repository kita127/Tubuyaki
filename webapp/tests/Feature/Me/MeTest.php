<?php

namespace Tests\Feature\Me;

use Tests\TestCase;
use App\Services\TubuyakiUser;

use Illuminate\Foundation\Testing\DatabaseTransactions;


class MeTest extends TestCase
{
    use DatabaseTransactions;
    public function test01_01_ログインしているユーザ情報取得(): void
    {
        $user = TubuyakiUser::create(
            id: 1,
            account_name: 'test_user',
            name: '検証太郎',
            email: 'test@example.com',
            password: '1111aaaa',
            remember_token: 'xxxxyyyy',
        );

        $response = $this->actingAs($user)->get('/api/user');
        $this->assertSame(
            [
                'id' => 1,
                'account_name' => 'test_user',
                'name' => '検証太郎',
                'email' => 'test@example.com',
            ],
            $response->json(),
            'passwordとremember_tokenは含まない'
        );
    }
}