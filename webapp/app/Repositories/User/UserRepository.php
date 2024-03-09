<?php

namespace App\Repositories\User;

use App\Entities\User;
use Illuminate\Support\Collection;

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

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, User>    key:ID
     */
    public function findAllBy(array $where): Collection;

    /**
     * @param array<string, array> $whereIn
     * @return Collection<int, User>    key:ID
     */
    public function findIn(array $whereIn): Collection;
}