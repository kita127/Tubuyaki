<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetail;

class UserDetailSeeder extends Seeder
{
    private bool $createdAdmin = false;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function (User $user) {
            if ($this->createdAdmin) {
                UserDetail::factory()->create(['user_id' => $user->id]);
            } else {
                UserDetail::factory()->create([
                    'user_id' => $user->id,
                    'account_name' => 'admin',
                    'name' => '管理者',
                    'email' => 'admin@example.net',
                ]);
                $this->createdAdmin = true;
            }
        });
    }
}
