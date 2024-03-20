<?php

namespace Tests\Lib;

use App\Entities\User;
use App\Entities\Identifiable\Unidentified;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;

class UserAssistance
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }
    public function createUser(
        string $account_name = 'test_user',
        string $name = '検証さん',
        string $email = 'test@example.net',
        string $password = 'password',
    ): TubuyakiUser {
        $user = new User(new Unidentified(), $account_name, $name, $email, $password);
        $user = $this->userRepository->save($user);
        return new TubuyakiUser($user);
    }

    /**
     * @return Collection<TubuyakiUser>
     */
    public function createUsers(int $count): Collection
    {
        $users = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $account_name = fake()->userName();
            $name = fake()->name();
            $email = fake()->address();
            $password = fake()->password();
            $u = $this->createUser($account_name, $name, $email, $password);
            $users->put($u->id->value(), $u);
        }
        return $users;
    }
}
