<?php

namespace App\Repositories\User;

use App\Entities\User;
use LogicException;
use OutOfBoundsException;

class MockUserRepository implements UserRepository
{
    /**
     * @var array<int, User>
     */
    private static array $dummyRecords = [];

    /**
     * @param User[] $users
     */
    public static function insert(array $users): void
    {
        foreach ($users as $user) {
            static::$dummyRecords[$user->id] = $user;
        }
    }

    public static function crearAll(): void
    {
        static::$dummyRecords = [];
    }

    public function find(int $id): User
    {
        if (!isset(static::$dummyRecords[$id])) {
            throw new OutOfBoundsException();
        }
        return static::$dummyRecords[$id];
    }

    public function save(User $user): bool
    {
        if ($user->id && isset(static::$dummyRecords[$user->id])) {
            // update
            static::$dummyRecords[$user->id] = $user;
        } else {
            // create
            $nextId = $this->getMaxId() + 1;
            $newUser = new User(
                id: $nextId,
                name: $user->name,
                email: $user->email,
                password: $user->password,
                remember_token: $user->remember_token,
            );
            static::$dummyRecords[$nextId] = $newUser;
        }
        return true;
    }

    /**
     * @param array<string, mixed> $where
     * @return User | null
     */
    public function findOneBy(array $where): never
    {
        throw new LogicException('unimplemented');
    }

    private function getMaxId(): int
    {
        return max(array_keys(static::$dummyRecords));
    }
}