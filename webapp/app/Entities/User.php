<?php

namespace App\Entities;

class User extends Entity
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}