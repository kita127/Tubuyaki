<?php

namespace App\Entities;

use App\Entities\Identifiable\Id;

class User extends Entity
{
    public function __construct(
        public readonly Id $id,
        public string $account_name,
        public string $name,
        public string $email,
        public string $password,
        public ?string $remember_token = null,
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