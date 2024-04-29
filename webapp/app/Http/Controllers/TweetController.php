<?php

namespace App\Http\Controllers;

use App\Entities\TweetType;
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
    const DEFAULT_INDEX = 0;
    const DEFAULT_COUNT = 30;

    public function __construct(
        private readonly TweetService $service,
        private readonly TweetRepository $tweetRepo,
        private readonly UserRepository $userRepo,
    ) {
    }

    public function getMyTweets(Request $request): JsonResponse
    {
        $index = $request->query('index') ?? self::DEFAULT_INDEX;
        $count = $request->query('count') ?? self::DEFAULT_COUNT;
        $me = $request->user();
        ['tweets' => $tweets, 'next' => $next] = $this->service->getTweets($me, $index, $count);
        $response = new \App\Http\Responses\Tweet\Tweets($tweets, $next);
        return response()->json([
            'contents' => $response->toArray(),
        ]);
    }

    public function getTweet(int $id): JsonResponse
    {
        $tweet = $this->service->getTweet($id);
        $response = \App\Http\Responses\Tweet\Tweet::create($tweet);
        return response()->json([
            'tweet' => $response->toArray(),
        ]);
    }

    public function getTweets(Request $request, int $id): JsonResponse
    {
        $index = $request->query('index') ?? self::DEFAULT_INDEX;
        $count = $request->query('count') ?? self::DEFAULT_COUNT;
        $ue = $this->userRepo->find($id);
        $user = new TubuyakiUser($ue);
        ['tweets' => $tweets, 'next' => $next] = $this->service->getTweets($user, $index, $count);
        $response = new \App\Http\Responses\Tweet\Tweets($tweets, $next);
        return response()->json([
            'contents' => $response->toArray(),
        ]);
    }

    public function post(TweetRequest $request): Response
    {
        /** @var TubuyakiUser $user */
        $user = $request->user();
        $text = $request->input('text');
        $this->service->post($user, $text, TweetType::Normal);
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

    public function retweet(Request $request, int $id): Response
    {
        $user = $request->user();
        $tweet = $this->tweetRepo->find($id);
        $this->service->retweet($tweet, $user);
        return response('', ResponseStatus::CREATED);
    }
}
