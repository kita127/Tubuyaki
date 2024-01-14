<?php

namespace Tests\Unit;

use App\Repositories\User\UserRepository;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLearning extends TestCase
{
    use DatabaseTransactions;

    public function test01_01_リポジトリ層を介してUserを取得する(): void
    {
        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $user = $repo->find(1);
        $this->assertSame(
            [
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => '$2y$10$4aSkjUUrTvuwcwBOAsw6a.N.pW.iJ2e0JPo48tSLAVLY9qmiq24F2',
            ],
            $user->toArray()
        );
    }
}