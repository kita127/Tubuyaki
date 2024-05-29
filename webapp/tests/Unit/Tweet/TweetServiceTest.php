<?php

namespace Test\Unit\Tweet;

use App\Repositories\User\MockUserRepository;
use App\Repositories\User\UserRepository;
use LogicException;
use Tests\TestCase;
use App\Entities\TweetType;
use App\Repositories\Tweet\MockTweetRepository;
use App\Repositories\Tweet\TweetRepository;
use App\Services\Tweet\TweetRetriever;
use App\Services\Tweet\TweetService;
use Tests\Lib\TweetCreator;
use Tests\Lib\UserAssistance;

class TweetServiceTest extends TestCase
{
    private readonly UserRepository $userRepository;
    private readonly TweetRepository $tweetRepository;
    private readonly UserAssistance $userAssistance;
    private readonly TweetCreator $tweetCreator;

    protected function setUp(): void
    {
        $this->tweetRepository = new MockTweetRepository();
        $this->userRepository = new MockUserRepository();
        $this->userAssistance = new UserAssistance($this->userRepository);
        $this->tweetCreator = new TweetCreator($this->userRepository, $this->tweetRepository);
    }

    public function test01_01_自分のつぶやきはリツイートできない(): void
    {
        $retriver = new TweetRetriever($this->tweetRepository, $this->userRepository);
        $service = new TweetService($this->tweetRepository, $this->userRepository, $retriver);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('自分のつぶやきにはリツイートできません');

        $users = $this->userAssistance->createUsers(1);
        $me = $users->shift();
        $targetTweet = $this->tweetCreator->create($me, '自分のつぶやき', TweetType::Normal);
        $service->retweet($targetTweet, $me);
    }
}
