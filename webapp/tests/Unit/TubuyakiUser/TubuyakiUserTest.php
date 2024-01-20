<?php

namespace Tests\Unit;

use App\Services\TubuyakiUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class TubuyakiUserTest extends TestCase
{
    use DatabaseTransactions;


    public function test01_01_TubuyakiUserでactingAsが使える(): void
    {
        $user = TubuyakiUser::createUser(
            id: 1,
            name: '検証太郎',
            email: 'test@example.com',
            password: '1111aaaa',
            remember_token: 'xxxxyyyy',

        );
        $this->actingAs($user);
        $actual = Auth::user();
        $this->assertTrue($actual instanceof TubuyakiUser);
    }
}