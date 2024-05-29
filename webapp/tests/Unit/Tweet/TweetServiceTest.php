<?php

namespace Test\Unit\Tweet;

use App\Repositories\User\MockUserRepository;
use LogicException;
use Tests\TestCase;
use App\Entities\TweetType;
use App\Repositories\Tweet\MockTweetRepository;
use App\Services\Tweet\TweetRetriever;
use App\Services\Tweet\TweetService;
use Tests\Lib\TweetCreator;
use Tests\Lib\UserAssistance;

class TweetServiceTest extends TestCase
{
    private readonly TweetRetriever $tweetRetriever;
    private readonly TweetCreator $tweetCreator;
    private readonly UserAssistance $userAssistance;
    private readonly TweetService $tweetService;

    protected function setUp(): void
    {
        $tweetRepository = new MockTweetRepository();
        $userRepository = new MockUserRepository();
        $this->userAssistance = new UserAssistance($userRepository);
        $this->tweetRetriever = new TweetRetriever($tweetRepository, $userRepository);
        $this->tweetCreator = new TweetCreator($userRepository, $tweetRepository);
        $this->tweetService = new TweetService($tweetRepository, $userRepository, $this->tweetRetriever);
    }

    public function test01_01_自分のつぶやきはリツイートできない(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('自分のつぶやきにはリツイートできません');

        $users = $this->userAssistance->createUsers(1);
        $me = $users->shift();
        $tweet = $this->tweetCreator->create($me, '自分のつぶやき', TweetType::Normal);
        $this->tweetService->retweet($tweet->id->value(), $me);
    }

    public function test01_02_一度リツイートしたつぶやきに対してリツイートできない(): void
    {
        $users = $this->userAssistance->createUsers(2);
        $me = $users->shift();
        $other = $users->shift();
        $tweet = $this->tweetCreator->create($other, '他人のつぶやき', TweetType::Normal);
        $this->tweetService->retweet($tweet->id->value(), $me);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('同じつぶやきに再度リツイートしています');
        $this->tweetService->retweet($tweet->id->value(), $me);
    }
}
