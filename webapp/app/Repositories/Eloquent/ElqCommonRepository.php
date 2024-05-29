<?php

namespace App\Repositories\Eloquent;

use App\Entities\Entity;
use App\Models\BaseModel;
use App\Repositories\Interface\Modifiable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use LogicException;

class ElqCommonRepository
{
    public function __construct(
        private readonly BaseModel $model,
    ) {
    }

    public function find(int $id): Entity
    {
        $elqUser = $this->model->findOrFail($id);
        return $elqUser->toEntity();
    }

    /**
     * @param array<string, mixed>|Builder $where
     * @return Collection<int, Entity>    key:ID
     */
    public function findAllBy(array|Builder $where): Collection
    {
        $query = $this->model->newQuery();
        if (is_array($where)) {
            $query = $this->createWhereQuery($query, $where);
        } elseif ($where instanceof Builder) {
            $query = $where;
        } else {
            throw new LogicException();
        }
        /** @var Collection<BaseModel> $models */
        $models = $query->get();
        return $models->map(fn (BaseModel $f) => $f->toEntity())->keyBy('id');
    }

    /**
     * @param array<string, mixed>|Builder $where
     * @return Entity
     */
    public function findOneBy(array|Builder $where): ?Entity
    {
        $query = $this->model->newQuery();
        if (is_array($where)) {
            $query = $this->createWhereQuery($query, $where);
        } elseif ($where instanceof Builder) {
            $query = $where;
        } else {
            throw new LogicException();
        }
        $model = $query->first();
        return $model?->toEntity();
    }

    /**
     * @param array<string, array> $whereIn
     * @return Collection<int, Entity>    key:ID
     */
    public function findIn(array $whereIn): Collection
    {
        $keys = array_keys($whereIn);
        if (count($keys) !== 1) {
            throw new LogicException();
        }
        $values = array_values($whereIn);
        if (count($values) !== 1) {
            throw new LogicException();
        }
        if (!is_array($values[0])) {
            throw new LogicException();
        }
        $query = $this->model->query()->whereIn($keys[0], $values[0]);
        /** @var Collection<BaseModel> $models */
        $models = $query->get();
        $result = collect([]);
        foreach ($models as $model) {
            $result->put($model->id, $model->toEntity());
        }
        return $result;
    }

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function save(Entity $entity, Modifiable $repo): Entity
    {
        if ($entity->id->isIdentified()) {
            // update
            return $repo->update($entity);
        } else {
            // create
            return $repo->create($entity);
        }
    }

    public function delete(Entity $entity): bool
    {
        $model = $this->model->findOrFail($entity->id->value());
        return (bool) $model->delete();
    }

    /**
     * @param Builder $query
     * @param array<string, mixed> $where
     * @return Builder
     */
    public function createWhereQuery(Builder $query, array $where): Builder
    {
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }
}
