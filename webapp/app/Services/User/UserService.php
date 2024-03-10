<?php

namespace App\Services\User;

use App\Entities\Identifiable\Unidentified;
use App\Entities\User;
use App\Repositories\User\UserRepository;
use LogicException;

class UserService
{
    public function __construct(
        private readonly UserRepository $repo,
    ) {
    }

    /**
     * @throws LogicException
     */
    public function store(?string $accountName, string $name, string $email, string $password): int
    {
        if (!$accountName) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $accountName = substr(str_shuffle($characters), 0, 30);
        }
        if ($this->existsAccountName($accountName)) {
            throw new LogicException('既に登録済みのアカウント名です');
        }
        if ($this->existsEmail($email)) {
            throw new LogicException('既に登録済みのメールアドレスです');
        }
        $entity = $this->repo->save(
            new User(new Unidentified(), $accountName, $name, $email, $password)
        );
        return $entity->id->value();
    }

    private function existsEmail(string $email): bool
    {
        return $this->repo->findOneBy(['email' => $email]) ? true : false;
    }

    private function existsAccountName(string $accountName): bool
    {
        return $this->repo->findOneBy(['account_name' => $accountName]) ? true : false;
    }
}