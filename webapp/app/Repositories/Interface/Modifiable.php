<?php

namespace App\Repositories\Interface;

use App\Entities\Entity;

interface Modifiable
{
    /**
     * @param Entity $entity
     * @return Entity
     */
    public function create(Entity $entity): Entity;

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function update(Entity $entity): Entity;
}