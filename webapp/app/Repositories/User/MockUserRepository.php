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


}