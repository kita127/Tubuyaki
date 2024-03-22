<?php

namespace App\Http\Controllers;

use App\Http\Constant\ResponseStatus;
use App\Http\Requests\Tweet\TweetRequest;
use App\Repositories\Tweet\TweetRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Tweet\TweetService;
use Illuminate\Http\Response;

class TweetController extends Controller
{
    public function __construct(
        private readonly TweetService $service,
        private readonly TweetRepository $tweetRepo,
        private readonly UserRepository $userRepo,
    ) {
    }

    public function getMyTweets(Request $request): JsonResponse
    {
        $me = $request->user();
        $result = $this->service->getTweets($me);
        return response()->json([
            'tweets' => $result->map(fn ($v) => [
                'text' => $v['text'],
                'created_at' => $v['created_at'],
                'updated_at' => $v['updated_at'],
            ])->values(),
        ], 200);
    }

    public function getTweets(int $id): JsonResponse
    {
        $ue = $this->userRepo->find($id);
        $user = new TubuyakiUser($ue);
        $tweets = $this->service->getTweets($user);
        return response()->json(
            [
                'tweets' => $tweets->values(),
            ]
        );
    }

    public function post(TweetRequest $request): Response
    {
        /** @var TubuyakiUser $user */
        $user = $request->user();
        $text = $request->input('text');
        $this->service->post($user, $text);
        return response('', ResponseStatus::CREATED);
    }

    public function getReplies(int $id): JsonResponse
    {
        $tweet = $this->tweetRepo->find($id);
        $replies = $this->service->getReplies($tweet);
        $response = \App\Http\Responses\Tweet\Replies::create($replies);
        return response()->json(
            [
                'replies' => $response->toArray()
            ]
        );
    }

    public function reply(TweetRequest $request, int $id): Response
    {
        $user = $request->user();
        $tweet = $this->tweetRepo->find($id);
        $text = $request->input('text');
        $this->service->reply($tweet, $user, $text);
        return response('', ResponseStatus::CREATED);
    }
}
