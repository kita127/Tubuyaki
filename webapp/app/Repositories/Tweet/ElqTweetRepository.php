<?php

namespace App\Repositories\Tweet;

use App\Repositories\Tweet\TweetRepository;
use App\Entities\Tweet;
use App\Models\Tweet as ElqTweet;

class ElqTweetRepository implements TweetRepository
{

    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    public function save(Tweet $tweet): Tweet
    {
        if ($tweet->id->isIdentified()) {
            // update
            return $this->update($tweet);
        } else {
            // create
            return $this->create($tweet);
        }
    }

    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    private function create(Tweet $tweet): Tweet
    {
        $elqTweet = new ElqTweet([
            'user_id' => $tweet->user_id,
            'text' => $tweet->text,
        ]);
        $elqTweet->save();
        return $elqTweet->toEntity();
    }

    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    private function update(Tweet $tweet): Tweet
    {
        /** @var ElqTweet $elqTweet */
        $elqTweet = ElqTweet::findOrFail($tweet->id->value());
        $elqTweet->user_id = $tweet->user_id;
        $elqTweet->text = $tweet->text;
        $elqTweet->save();
        return $elqTweet->toEntity();
    }
}