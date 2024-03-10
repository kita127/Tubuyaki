<?php

namespace App\Entities\Identifiable;

class Identified implements Id
{
    public function __construct(
        private readonly int $value,
    ) {
    }
    public function value(): int
    {
        return $this->value;
    }

    public function isIdentified(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}