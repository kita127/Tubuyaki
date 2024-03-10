<?php

namespace Tests\Lib;

use App\Entities\User;
use App\Entities\Identifiable\Unidentified;
use App\Repositories\User\UserRepository;

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
    ): User {
        $user = new User(new Unidentified(), $account_name, $name, $email, $password);
        return $this->userRepository->save($user);
    }
}