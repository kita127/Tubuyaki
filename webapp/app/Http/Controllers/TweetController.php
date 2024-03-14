<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Tweet\TweetService;

class TweetController extends Controller
{
    public function __construct(
        private readonly TweetService $service,
    ) {
    }
    public function getMyTweets(Request $request): JsonResponse
    {
        $me = $request->user();
        $result = $this->service->getTweets($me);
        return response()->json([
            'tweets' => $result->map(fn($v) => [
                'text' => $v['text'],
                'created_at' => $v['created_at'],
                'updated_at' => $v['updated_at'],
            ])->values(),
        ], 200);
    }
}
