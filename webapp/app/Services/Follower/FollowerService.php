<?php

namespace App\Services\Follower;

use App\Entities\Follower;
use App\Entities\User;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Collection;

class FollowerService
{
    public function __construct(
        private readonly FollowerRepository $followerRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getFollowees(int $id): Collection
    {
        $followRelations = $this->followerRepository->findAllBy(['user_id' => $id]);
        $followeeIdLs = $followRelations->map(fn(Follower $follower) => $follower->followee_id);
        $followees = $this->userRepository->findIn(['id' => $followeeIdLs->all()]);
        return $followees->map(fn(User $user) => [
            // TODO: ここはクラス化を検討する？
            'id' => $user->id,
            'account_name' => $user->account_name,
            'name' => $user->name,
        ]);
    }
}