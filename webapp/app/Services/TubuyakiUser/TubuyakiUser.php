<?php

namespace App\Services\TubuyakiUser;

use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Entities\User as UserEntity;

class TubuyakiUser implements Authenticatable
{
    public static function create(
        ?int $id,
        string $name,
        string $email,
        string $password,
        ?string $remember_token
    ): static {
        $entity = new UserEntity(
            id: $id,
            name: $name,
            email: $email,
            password: $password,
            remember_token: $remember_token,
        );
        return new static(entity: $entity);
    }

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
    public function getRememberToken(): ?string
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