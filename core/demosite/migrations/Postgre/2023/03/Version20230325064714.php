<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\User;
use App\Service\MigrationsFactory\PasswordHashingMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325064714 extends AbstractMigration implements PasswordHashingMigration
{
    private UserPasswordHasherInterface $passwordHasher;

    public function getDescription(): string
    {
        return 'Добавим администратора';
    }

    public function up(Schema $schema): void
    {
        $password = 'admin';
        $this->connection->executeQuery('INSERT INTO users (email, password, roles) VALUES (?,?,?)', [
            'admin@demosite.com',
            $this->passwordHasher->hashPassword((new User()), $password),
            json_encode(['ROLE_ADMIN'], JSON_THROW_ON_ERROR)
        ]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @return void
     */
    public function setPasswordHasher(UserPasswordHasherInterface $passwordHasher): void
    {
        $this->passwordHasher = $passwordHasher;
    }
}
