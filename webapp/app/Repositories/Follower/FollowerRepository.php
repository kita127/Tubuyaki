<?php

namespace App\Repositories\Follower;

use Illuminate\Support\Collection;
use App\Entities\Follower;

interface FollowerRepository
{
    /**
     * @param Follower $follower
     * @return Follower
     */
    public function save(Follower $follower): Follower;

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Follower>    key:ID
     */
    public function findAllBy(array $where): Collection;

    /**
     * @param array<string, mixed> $where
     * @return Follower
     */
    public function findOneBy(array $where): ?Follower;

    public function delete(Follower $follower): bool;

}