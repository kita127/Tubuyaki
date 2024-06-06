<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Identified;
use App\Entities\Identifiable\Unidentified;
use App\Entities\User;
use App\Repositories\User\UserRepository;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * LoginController::authenticate
     */
    public function test01_01_メールアドレスとパスが一致したらログイン後にリダイレクト(): void
    {
        // 準備
        $user = $this->createUser();

        // 実行
        $response = $this->post('/api/login', [
            'email' => 'test_user@example.com',
            'password' => 'testuserpass',
        ]);

        // 検証
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->json();
        $this->assertSame(
            [
                'id' => $user->id->value(),
                'account_name' => 'test_user',
                'name' => '検証次郎',
                'email' => 'test_user@example.com',
            ],
            $content
        );
    }

    /**
     * LoginController::authenticate
     */
    public function test01_02_パスが不一致はリダイレクト(): void
    {
        // 準備
        $user = $this->createUser();

        // 実行
        $response = $this->post('/api/login', [
            'email' => 'test_user@example.com',
            'password' => 'invalid',
        ]);

        // 検証
        $this->assertSame(401, $response->getStatusCode());
    }

    public function test01_03_emailが不一致はリダイレクト(): void
    {
        // 準備
        $user = $this->createUser();

        // 実行
        $response = $this->post('/api/login', [
            'email' => 'test_user@example.hoge',
            'password' => 'testuserpass',
        ]);

        // 検証
        $this->assertSame(401, $response->getStatusCode());
    }

    public function test02_01_未ログイン時に認証の必要なAPIにはアクセスできない(): void
    {
        $response = $this->get("/api/users/me");
        $response->assertStatus(302);
    }

    //////////////////////////////////////
    // private
    //////////////////////////////////////

    private function createUser(): User
    {
        $user = new User(
            id: new Unidentified(),
            account_name: 'test_user',
            name: '検証次郎',
            email: 'test_user@example.com',
            password: 'testuserpass',
        );
        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $user = $repo->save($user);
        return $user;
    }
}
