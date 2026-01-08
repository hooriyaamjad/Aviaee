<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\UserEntity;

interface IUserRepository
{
    /**
     * Returns ['id', 'password'] or null for login
     */
    public function findByEmail(string $email): ?array;

    /**
     * Creates a user in the database and returns the new entity
     */
    public function create(UserEntity $user, string $hashedPassword): UserEntity;
}
