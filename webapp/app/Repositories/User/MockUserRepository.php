<?php

namespace App\Repositories\User;

use App\Entities\User;
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

    public function save(User $user): User
    {
        if ($user->id && isset(static::$dummyRecords[$user->id])) {
            // update
            static::$dummyRecords[$user->id] = $user;
            return $user;
        } else {
            // create
            $nextId = $this->getMaxId() + 1;
            $newUser = new User(
                id: $nextId,
                account_name: $user->account_name,
                name: $user->name,
                email: $user->email,
                password: $user->password,
                remember_token: $user->remember_token,
            );
            static::$dummyRecords[$nextId] = $newUser;
            return $newUser;
        }
    }

    /**
     * @param array<string, mixed> $where
     * @return User | null
     */
    public function findOneBy(array $where): ?User
    {
        foreach (static::$dummyRecords as $record) {
            if ($this->match($record, $where)) {
                return $record;
            }
        }
        return null;
    }

    /**
     * @param User $user
     * @param array<string, mixed> $where
     * @return bool
     */
    private function match(User $user, array $where): bool
    {
        foreach ($where as $key => $value) {
            if (!property_exists($user, $key)) {
                return false;
            }
            if ($user->$key !== $value) {
                return false;
            }
        }
        return true;
    }

    private function getMaxId(): int
    {
        if (count(static::$dummyRecords) <= 0) {
            return 0;
        }
        return max(array_keys(static::$dummyRecords));
    }
}