<?php

namespace App\Entities;

use Illuminate\Contracts\Support\Arrayable;

abstract class Entity implements Arrayable
{
    public function toArray(): array
    {
        return (array) $this;
    }
}