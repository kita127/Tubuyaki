<?php

namespace App\Http\Controllers\Follow;

use App\Http\Constant\ResponseStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\User\UserRepository;
use App\Services\Follow\FollowService;
use App\Services\TubuyakiUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class FollowController extends Controller
{
    public function __construct(
        private readonly FollowerRepository $followerRepo,
        private readonly UserRepository $userRepo,
        private readonly FollowService $service,
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getFollowees(int $id): JsonResponse
    {
        $ue = $this->userRepo->find($id);
        $user = new TubuyakiUser($ue);
        $followees = $this->service->getFollowees($user);
        return response()->json(
            [
                'followees' => $followees->values(),
            ],
        );
    }

    public function getFollowers(int $id): JsonResponse
    {
        $ue = $this->userRepo->find($id);
        $user = new TubuyakiUser($ue);
        $followers = $this->service->getFollowers($user);
        return response()->json(
            [
                'followers' => $followers->values(),
            ],
        );
    }

    public function follow(Request $request, int $id): Response
    {
        $target = $this->userRepo->find($id);
        /** @var Authenticatable $user */
        $user = $request->user();
        $this->service->follow($user, $target);
        return response('', ResponseStatus::CREATED);
    }

    public function unfollow(Request $request, int $id): Response
    {
        $target = $this->userRepo->find($id);
        /** @var Authenticatable $user */
        $user = $request->user();
        $this->service->unfollow($user, $target);
        return response('', ResponseStatus::NO_CONTENT);
    }

    public function getMyFollowees(Request $request): JsonResponse
    {
        /** @var TubuyakiUser $user */
        $user = $request->user();
        $followees = $this->service->getFollowees($user);
        return response()->json(
            [
                'followees' => $followees->values(),
            ]
        );
    }

    public function getMyFollowers(Request $request): JsonResponse
    {
        /** @var TubuyakiUser $user */
        $user = $request->user();
        $followers = $this->service->getFollowers($user);
        return response()->json(
            [
                'followers' => $followers->values(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
