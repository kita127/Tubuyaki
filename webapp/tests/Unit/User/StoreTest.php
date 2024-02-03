<?php

namespace Test\Unit\User;

use App\Entities\User;
use App\Repositories\User\MockUserRepository;
use App\Repositories\User\UserRepository;
use App\Services\User\UserService;
use LogicException;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected function setUp(): void
    {
        app()->bind(UserRepository::class, MockUserRepository::class);
    }

    public function test01_01_同じアカウント名のユーザーがすでにいる場合は登録できない(): void
    {
        $repo = new MockUserRepository();
        $repo->save(
            new User(id: 1, account_name: 'existing_user', name: '登録済の人', email: 'test@example.com', password: 'aabb1111'),
        );

        $service = new UserService($repo);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('既に登録済みのアカウント名です');
        $service->store('existing_user', 'あたらしいひと', 'test02@example.com', 'ccdd2222');
    }
}