<?php

namespace Tests\Unit\User;

use App\Entities\User;
use App\Repositories\User\MockUserRepository;
use App\Repositories\User\UserRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLearning extends TestCase
{
    use DatabaseTransactions;

    public function test01_01_リポジトリ層を介してUserを取得する(): void
    {
        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $user = $repo->find(1);
        $this->assertTrue($user instanceof \App\Entities\User);
        $this->assertSame(
            [
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => $user->password,  // パスワードはハッシュが変わるのでみない
            ],
            $user->toArray()
        );
    }

    public function test01_02_Mockに置き換えてリポジトリからUserを取得する(): void
    {
        app()->bind(UserRepository::class, function () {
            return new MockUserRepository();
        });

        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $this->assertTrue($repo instanceof MockUserRepository);
        MockUserRepository::insert([
            new User(id: 1, name: 'モック1', email: 'mock@example.com', password: 'mock-password'),
        ]);

        $user = $repo->find(1);
        $this->assertTrue($user instanceof \App\Entities\User);
        $this->assertSame(
            [
                'id' => 1,
                'name' => 'モック1',
                'email' => 'mock@example.com',
                'password' => 'mock-password',
            ],
            $user->toArray()
        );

    }
}