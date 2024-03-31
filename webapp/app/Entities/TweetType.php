<?php

namespace App\Entities;

enum TweetType: string
{
    case Normal = 'normal';
    case Retweet = 'retweet';
    case Reply = 'reply';
}
