<?php

namespace App\Repositories\Traits\Eloquent;

use App\Entities\Entity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ElqCommonFeature
{
    private readonly Model $model;
    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Entity>    key:ID
     */
    public function findAllBy(array $where): Collection
    {
        $query = $this->createWhereQuery($where);
        $models = $query->get();
        // TODO: toEntiry()を保証するためインタフェース化すること
        return $models->map(fn(Model $f) => $f->toEntity())->keyBy('id');
    }

    /**
     * @param array<string, mixed> $where
     * @return Builder
     */
    private function createWhereQuery(array $where): Builder
    {
        $query = $this->model->newQuery();
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }

}