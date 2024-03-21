<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;

class Tweet implements Arrayable
{
    public function __construct(
        private readonly int $id,
        private readonly string $text,
        private readonly string $created_at,
        private readonly string $updated_at,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
