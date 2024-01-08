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
    public function test01_01_メールアドレスとパスが一致したらリダイレクト(): void
    {
        $user = new User([
            'name' => 'test_user',
            'email' => 'test_user@example.com',
            'password' => Hash::make('testuserpass'),
        ]);
        $user->save();

        $response = $this->post('/login', [
            'email' => 'test_user@example.com',
            'password' => 'testuserpass',
        ]);

        $this->assertSame(302, $response->getStatusCode(), '認証成功後はリダイレクト');
        //        $this->assertSame('', $response->headers);
    }

    /**
     * LoginController::authenticate
     */
    // public function test01_02_パスが不一致はxxx(): void
    // {
    //     $response = $this->post('/login', [
    //         'email' => 'admin@example.com',
    //         'invalidpass',
    //     ]);

    //     $this->assertSame(302, $response->getStatusCode(), '認証成功後はリダイレクト');
    //     $this->assertSame([], $response->headers);
    // }
}
