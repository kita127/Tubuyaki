<?php

namespace App\Auth;

use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use LogicException;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class TubuyakiUserProvider implements UserProvider
{
    public function __construct(
        private readonly HasherContract $hasher,
    ) {
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        $repo = app()->make(UserRepository::class);
        $entity = $repo->find($identifier);
        return new TubuyakiUser(entity: $entity);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $entity = $repo->find($identifier);
        $rememberToken = $entity->remember_token;

        return $rememberToken
            && hash_equals($rememberToken, $token) ? new TubuyakiUser(entity: $entity) : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $user->setRememberToken($token);

        if (!($user instanceof TubuyakiUser)) {
            throw new LogicException();
        }
        $repo = app()->make(UserRepository::class);
        $user->save($repo);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $credentials = array_filter(
            $credentials,
            fn($key) => !str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return null;
        }
        /** @var UserRepository $repo */
        $repo = app()->make(UserRepository::class);
        $entity = $repo->findOneBy($credentials);
        return new TubuyakiUser($entity);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

}