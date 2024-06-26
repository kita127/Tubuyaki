<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Entities\Entity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// もともと使用していたAuthenticatableクラス
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserDetail;
use App\Entities\Identifiable\Identified;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class);
    }

    public function toEntity(): Entity
    {
        return new \App\Entities\User(
            id: new Identified($this->id),
            account_name: $this->userDetail->account_name,
            name: $this->userDetail->name,
            email: $this->userDetail->email,
            password: $this->userDetail->password,
            remember_token: $this->remember_token,
        );
    }
}
