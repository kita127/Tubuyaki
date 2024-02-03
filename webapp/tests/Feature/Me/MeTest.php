<?php

namespace Tests\Feature\Me;

use App\Repositories\User\UserRepository;
use Tests\TestCase;
use App\Services\TubuyakiUser;

use Illuminate\Foundation\Testing\DatabaseTransactions;


class MeTest extends TestCase
{
    use DatabaseTransactions;
    public function test01_01_ログインしているユーザ情報取得(): void
    {
        $user = TubuyakiUser::create(
            app()->make(UserRepository::class),
            account_name: 'test_user',
            name: '検証太郎',
            email: 'test@example.com',
            password: '1111aaaa',
            remember_token: 'xxxxyyyy',
        );

        $response = $this->actingAs($user)->get('/api/user');
        $content = $response->json();
        $this->assertSame(
            [
                'id' => $content['id'],
                'account_name' => 'test_user',
                'name' => '検証太郎',
                'email' => 'test@example.com',
            ],
            $content,
            'passwordとremember_tokenは含まない'
        );
    }
}