<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepository;
use App\Services\Timeline\TimelineService;
use App\Services\TubuyakiUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function __construct(
        private readonly TimelineService $service,
        private readonly UserRepository $userRepository,
    ) {
    }
    public function getTimeline(int $id): JsonResponse
    {
        $user = new TubuyakiUser($this->userRepository->find($id));
        $timeline = $this->service->getTimeline($user);
        $response = \App\Http\Responses\Timeline\TimelineContents::create($timeline);
        return response()->json(
            [
                'contents' => $response->toArray(),
            ],
        );
    }
}
