<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;

class User implements Arrayable
{
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
