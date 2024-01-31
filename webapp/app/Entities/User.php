<?php

namespace App\Entities;

class User extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public string $account_name,
        public string $name,
        public string $email,
        public string $password,
        public ?string $remember_token,
    ) {
    }

    public function getIdentifierName(): string
    {
        return 'id';
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}