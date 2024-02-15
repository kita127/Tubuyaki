<?php

namespace App\Http\Controllers\Follow;

use App\Http\Controllers\Controller;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\User\UserRepository;
use App\Services\Follower\FollowerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(int $id): JsonResponse
    {
        $r1 = app()->make(FollowerRepository::class);
        $r2 = app()->make(UserRepository::class);
        $service = new FollowerService($r1, $r2);
        $followees = $service->getFollowees($id);
        return response()->json(
            [
                'followees' => $followees->values(),
            ],
            200,
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
