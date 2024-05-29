<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Id;
use App\Http\Responses\Tweet\Tweet as Response;

interface Tweet
{
    public function id(): Id;
    public function createResponse(): Response;
}
