<?php

namespace App\Services;

use App\Entities\Follower;
use App\Entities\Identifiable\Unidentified;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Entities\User as UserEntity;
use Illuminate\Contracts\Support\Arrayable;
use App\Entities\Identifiable\Id;
use App\Repositories\Follower\FollowerRepository;

class TubuyakiUser implements Authenticatable, Arrayable
{
    public readonly Id $id;

    public static function create(
        UserRepository $repo,
        string $account_name,
        string $name,
        string $email,
        string $password,
        ?string $remember_token
    ): static {
        $entity = new UserEntity(
            id: new Unidentified(),
            account_name: $account_name,
            name: $name,
            email: $email,
            password: $password,
            remember_token: $remember_token,
        );
        return new static(entity: $repo->save($entity));
    }

    public function __construct(
        private readonly UserEntity $entity,
    ) {
        $this->id = $this->entity->id;
    }

    public function accountName(): string
    {
        return $this->entity->account_name;
    }

    public function name(): string
    {
        return $this->entity->name;
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
        return (string)$this->entity->id->value();
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

    public function toArray(): array
    {
        return $this->entity->toArray();
    }

    public function toRistrictedArray(): array
    {
        $array = $this->toArray();
        unset($array['password']);
        unset($array[$this->entity->getRememberTokenName()]);
        return $array;
    }

    public function getEntity(): UserEntity
    {
        return $this->entity;
    }

    public function follow(TubuyakiUser $target, FollowerRepository $repo): void
    {
        $followRelation = new Follower(new Unidentified(), $this->id->value(), $target->id->value());
        $repo->save($followRelation);
    }
}
