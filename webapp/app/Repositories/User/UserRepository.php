<?php

namespace App\Repositories\User;

use App\Entities\User;

interface UserRepository
{
    public function find(int $id): User;

    /**
     * @return User
     */
    public function save(User $user): User;

    /**
     * @param array<string, mixed> $where
     * @return User | null
     */
    public function findOneBy(array $where): ?User;
}