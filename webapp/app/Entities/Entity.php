<?php

namespace App\Entities;

use Illuminate\Contracts\Support\Arrayable;
use App\Entities\Identifiable\Id;

abstract class Entity implements Arrayable
{
    public function __construct(
        public readonly Id $id,
    ) {
    }

    public function toArray(): array
    {
        $array = (array) $this;
        $array['id'] = $this->id->isIdentified() ? $this->id->value() : null;
        return $array;
    }
}