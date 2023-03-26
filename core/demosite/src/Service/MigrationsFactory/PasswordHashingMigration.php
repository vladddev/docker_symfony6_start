<?php

namespace App\Service\MigrationsFactory;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

interface PasswordHashingMigration
{
    public function setPasswordHasher(UserPasswordHasherInterface $passwordHasher): void;
}