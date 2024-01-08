<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
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
        $response = $this->post('/login', [
            'email' => 'test_user@example.com',
            'password' => 'testuserpass',
        ]);

        // 検証
        $this->assertSame(302, $response->getStatusCode(), '認証成功後はリダイレクト');
        $response->assertRedirect('welcom');
    }

    /**
     * LoginController::authenticate
     */
    public function test01_02_パスが不一致はルートにリダイレクト(): void
    {
        // 準備
        $user = $this->createUser();

        // 実行
        $response = $this->post('/login', [
            'email' => 'test_user@example.com',
            'password' => 'invalid',
        ]);

        // 検証
        $this->assertSame(302, $response->getStatusCode(), '認証成功後はリダイレクト');
        $response->assertRedirect('');
    }

    //////////////////////////////////////
    // private
    //////////////////////////////////////

    private function createUser(array $params = []): User
    {
        $user = new User(array_merge([
            'name' => 'test_user',
            'email' => 'test_user@example.com',
            'password' => Hash::make('testuserpass'),
        ], $params));
        $user->save();
        return $user;
    }
}
