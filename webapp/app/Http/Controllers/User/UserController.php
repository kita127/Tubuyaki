<?php

namespace App\Http\Controllers\User;

use App\Entities\User;
use App\Http\Constant\ResponseStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest;
use App\Repositories\User\UserRepository;
use App\Services\User\UserService;
use App\Services\TubuyakiUser;
use LogicException;

class UserController extends Controller
{
    public function users(Request $request): JsonResponse
    {
        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $users = $repo->findAllBy([]);
        $array = $users->map(fn (User $u) => $u->name)->toArray();
        return response()->json(
            $array,
            200,
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var TubuyakiUser $user */
        $user = $request->user();
        return response()->json(
            $user->toArray(),
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRequest  $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $accountName = $request->input('account_name');
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        /** @var UserService $service */
        $service = app()->make(UserService::class);
        try {
            $id = $service->store($accountName, $name, $email, $password);
        } catch (LogicException $e) {
            return response()->json([
                'id' => null,
                'message' => $e->getMessage(),
            ], ResponseStatus::CONFLICT);
        }
        return response()->json([
            'id' => $id,
            'message' => '',
        ], ResponseStatus::CREATED);
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
