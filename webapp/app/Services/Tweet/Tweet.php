<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Id;
use App\Http\Responses\Tweet\Tweet as Response;
use App\Services\TubuyakiUser;
use App\Entities\Tweet as EntitysTweet;

interface Tweet
{
    public function id(): Id;
    public function createResponse(): Response;
    public function isOwner(TubuyakiUser $user): bool;
    public function entity(): EntitysTweet;
}
