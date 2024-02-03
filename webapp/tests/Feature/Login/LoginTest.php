<?php

namespace Tests\Feature\Login;

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
        // TODO: Eloquent直接触るのやめる
        // 準備
        $user = $this->createUser();

        // 実行
        $response = $this->post('/login', [
            'email' => 'test_user@example.com',
            'password' => 'testuserpass',
        ]);

        // 検証
        $this->assertSame(302, $response->getStatusCode(), '認証成功後はリダイレクト');
        $response->assertRedirect('');
        $response->assertSessionHasNoErrors();
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
        $response->assertSessionHasErrors(['email' => 'The provided credentials do not match our records.']);
    }

    //////////////////////////////////////
    // private
    //////////////////////////////////////

    private function createUser(array $params = []): User
    {
        $user = new User(array_merge([
            'account_name' => 'test_user',
            'name' => '検証次郎',
            'email' => 'test_user@example.com',
            'password' => 'testuserpass',
        ], $params));
        $user->save();
        return $user;
    }
}
