<?php

namespace App\Services\User;

use App\Entities\User;
use App\Repositories\User\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $repo,
    ) {
    }
    public function store(?string $accountName, string $name, string $email, string $password): int
    {
        if (!$accountName) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $accountName = substr(str_shuffle($characters), 0, 30);
        }
        $entity = $this->repo->save(
            new User(null, $accountName, $name, $email, $password)
        );
        return $entity->id;
    }
}