<?php

namespace App\Services\TubuyakiUser;

use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Entities\User as UserEntity;

class TubuyakiUser implements Authenticatable
{
    public function __construct(
        private UserEntity $entity,
    ) {

    }
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->entity->getIdentifierName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier(): string
    {
        return $this->entity->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->entity->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        return $this->entity->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value): void
    {
        $this->entity->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return $this->entity->getRememberTokenName();
    }

    public function save(UserRepository $repo): void
    {
        $repo->save($this->entity);
    }

}