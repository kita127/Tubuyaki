<?php

namespace App\Entities;

abstract class Entity
{
    public function toArray(): array
    {
        return (array) $this;
    }
}