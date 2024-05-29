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
    private readonly TweetRetriever $tweetRetriever;
    private readonly TweetCreator $tweetCreator;

    protected function setUp(): void
    {
        $this->tweetRepository = new MockTweetRepository();
        $this->userRepository = new MockUserRepository();
        $this->userAssistance = new UserAssistance($this->userRepository);
        $this->tweetRetriever = new TweetRetriever($this->tweetRepository, $this->userRepository);
        $this->tweetCreator = new TweetCreator($this->userRepository, $this->tweetRepository);
    }

    public function test01_01_自分のつぶやきはリツイートできない(): void
    {
        $service = $this->createService();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('自分のつぶやきにはリツイートできません');

        $users = $this->userAssistance->createUsers(1);
        $me = $users->shift();
        $tweet = $this->tweetCreator->create($me, '自分のつぶやき', TweetType::Normal);
        $service->retweet($tweet->id->value(), $me);
    }

    // TODO: FeatureTestも書く
    public function test01_02_一度リツイートしたつぶやきに対してリツイートできない(): void
    {
        $service = $this->createService();

        $users = $this->userAssistance->createUsers(2);
        $me = $users->shift();
        $other = $users->shift();
        $tweet = $this->tweetCreator->create($other, '他人のつぶやき', TweetType::Normal);
        $service->retweet($tweet->id->value(), $me);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('同じつぶやきに再度リツイートしています');
        $service->retweet($tweet->id->value(), $me);
    }

    private function createService(): TweetService
    {
        return new TweetService($this->tweetRepository, $this->userRepository, $this->tweetRetriever);
    }
}
