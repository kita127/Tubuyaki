<?php

namespace App\Http\Responses;

use App\Services\TubuyakiUser;
use Illuminate\Contracts\Support\Arrayable;

class User implements Arrayable
{
    public static function create(TubuyakiUser $user): static
    {
        return new User(
            $user->id->value(),
            $user->accountName(),
            $user->name(),
        );
    }

    public function __construct(
        private readonly int $id,
        private readonly string $account_name,
        private readonly string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'account_name' => $this->account_name,
            'name' => $this->name,
        ];
    }
}
