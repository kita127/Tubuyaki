<?php

namespace Tests\Feature\User;

use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * UserController::store
     */
    public function test01_01_ユーザー登録(): void
    {
        $response = $this->post('/api/user', [
            'account_name' => 'newUser',
            'name' => '新規追加',
            'email' => 'test@example.com',
            'password' => 'aabbcc123',
        ]);
        $response->assertStatus(201);
        $id = $this->assertReturnId($response->json());

        /** @var UserRepository $repo */
        $entity = app()->make(UserRepository::class)->find($id);
        $this->assertSame(
            [
                'id' => $id,
                'account_name' => 'newUser',
                'name' => '新規追加',
                'email' => 'test@example.com',
                'password' => $entity->password,
                'remember_token' => null,
            ],
            $entity->toArray()
        );
        $this->assertTrue(Hash::check('aabbcc123', $entity->password));
    }

    /**
     * UserController::store
     */
    public function test01_02_ユーザ登録でアカウント名未指定は英数字のランダムな値を設定(): void
    {
        $response = $this->post('/api/user', [
            //            'account_name' => 'newUser',
            'name' => '新規追加',
            'email' => 'test@example.com',
            'password' => 'aabbcc123',
        ]);
        $response->assertStatus(201);
        $id = $this->assertReturnId($response->json());

        /** @var UserRepository $repo */
        $entity = app()->make(UserRepository::class)->find($id);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $entity->account_name);
    }

    private function assertReturnId(array $json): int
    {
        $this->assertArrayHasKey('id', $json);
        $id = (int) $json['id'];
        $this->assertTrue($id > 0);
        return $id;
    }
}