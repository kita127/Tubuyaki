<?php

namespace App\Entities\Identifiable;

interface Id 
{
    public function value(): int;

    public function isIdentified(): bool;

    public function __toString(): string;
}
