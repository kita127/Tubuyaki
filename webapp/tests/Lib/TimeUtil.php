<?php

namespace Tests\Lib;

use Carbon\Carbon;

class TimeUtil
{
    public static function addTheDate(int $days, Carbon $date = null): void
    {
        $now = $date ? $date->copy() : Carbon::now();
        $now = $now->addDays($days);
        Carbon::setTestNow($now);
    }
}
