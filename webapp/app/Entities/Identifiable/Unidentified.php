<?php

namespace App\Entities\Identifiable;

use LogicException;

class Unidentified implements Id
{
    public function value(): never
    {
        throw new LogicException('Unidentified entity');
    }

    public function isIdentified(): bool
    {
        return false;
    }

    public function equal(Id $id): bool
    {
        return !$id->isIdentified;
    }

    public function __toString(): string
    {
        return '';
    }
}
