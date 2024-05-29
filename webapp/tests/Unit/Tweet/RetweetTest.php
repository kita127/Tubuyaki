<?php

namespace Test\Unit\Tweet;

use App\Repositories\User\MockUserRepository;
use App\Repositories\User\UserRepository;
use LogicException;
use Tests\TestCase;
use App\Entities\Identifiable\Unidentified;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Repositories\Tweet\MockTweetRepository;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use App\Services\Tweet\TweetRetriever;
use App\Services\Tweet\TweetService;
use Tests\Lib\UserAssistance;

class RetweetTest extends TestCase
{
    private readonly UserRepository $userRepository;
    private readonly TweetRepository $tweetRepository;
    private readonly UserAssistance $userAssistance;

    protected function setUp(): void
    {
        $this->tweetRepository = new MockTweetRepository();
        $this->userRepository = new MockUserRepository();
        $this->userAssistance = new UserAssistance($this->userRepository);
    }

    public function test01_01_自分のつぶやきはリツイートできない(): void
    {
        $retriver = new TweetRetriever($this->tweetRepository, $this->userRepository);
        $service = new TweetService($this->tweetRepository, $this->userRepository, $retriver);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('自分のつぶやきにはリツイートできません');

        $users = $this->userAssistance->createUsers(1);
        $me = $users->shift();
        $targetTweet = $this->createTweet($me, '自分のつぶやき', TweetType::Normal);
        $service->retweet($targetTweet, $me);
    }

    // TODO: ライブラリにする
    private function createTweet(TubuyakiUser $user, string $content, TweetType $type): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $type, $content, new Unidentified());
        return $this->tweetRepository->save($tweet);
    }
}
