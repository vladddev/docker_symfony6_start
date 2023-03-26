<?php

declare(strict_types=1);

namespace App\Service\MigrationsFactory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MigrationFactoryDecorator implements MigrationFactory
{
    public function __construct(
        private MigrationFactory            $migrationFactory,
        private ContainerInterface          $container,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $instance = $this->migrationFactory->createVersion($migrationClassName);

        switch (true) {
            case $instance instanceof ContainerAwareInterface:
                $instance->setContainer($this->container);
                break;
            case $instance instanceof PasswordHashingMigration:
                $instance->setPasswordHasher($this->passwordHasher);
        }

        return $instance;
    }
}