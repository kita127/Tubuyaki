<?php

namespace App\Http\Controllers\Follow;

use App\Http\Controllers\Controller;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\User\UserRepository;
use App\Services\Follower\FollowerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class FollowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(int $id): JsonResponse
    {
        $followerRepo = app()->make(FollowerRepository::class);
        $userRepo = app()->make(UserRepository::class);
        $service = new FollowerService($followerRepo, $userRepo);
        $followees = $service->getFollowees($id);
        return response()->json(
            [
                'followees' => $followees->values(),
            ],
            200,
        );
    }

    public function getFollowers(int $id): JsonResponse
    {
        $followerRepo = app()->make(FollowerRepository::class);
        $userRepo = app()->make(UserRepository::class);
        $service = new FollowerService($followerRepo, $userRepo);
        $followers = $service->getFollowers($id);
        return response()->json(
            [
                'followers' => $followers->values(),
            ],
            200,
        );
    }

    public function follow(Request $request, int $id): Response
    {
        /** @var FollowerService $service */
        $service = app()->make(FollowerService::class);
        /** @var UserRepository $userRepo */
        $userRepo = app()->make(UserRepository::class);
        $target = $userRepo->find($id);
        /** @var Authenticatable $user */
        $user = $request->user();
        $service->follow($user, $target);
        // TODO: レスポンスコードも定数化する
        return response('', 201);
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
