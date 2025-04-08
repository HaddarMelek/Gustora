<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250408110012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create role, user, and user_roles tables and insert default roles and admin user';
    }

    public function up(Schema $schema): void
    {
        // Create 'role' table
        $this->addSql(<<<'SQL'
            CREATE TABLE role (
                id INT AUTO_INCREMENT NOT NULL,
                role VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Create 'user' table
        $this->addSql(<<<'SQL'
            CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                password VARCHAR(255) NOT NULL,
                country_code VARCHAR(255) NOT NULL,
                phone_number VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Create 'user_roles' table
        $this->addSql(<<<'SQL'
            CREATE TABLE user_roles (
                user_id INT NOT NULL,
                role_id INT NOT NULL,
                INDEX IDX_54FCD59FA76ED395 (user_id),
                INDEX IDX_54FCD59FD60322AC (role_id),
                PRIMARY KEY(user_id, role_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Add foreign keys
        $this->addSql(<<<'SQL'
            ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE
        SQL);

        // Insert roles
        $this->addSql("INSERT INTO role (role) VALUES ('ROLE_ADMIN'), ('ROLE_USER')");

        // Insert admin user
        $this->addSql(<<<'SQL'
            INSERT INTO user (email, password, country_code, phone_number)
            VALUES (
                'admin@example.com',
                '$2y$13$imCZgco9PGOvMK2dqxWE6OO1GOcMVIDmcP8QU4Fpffzb0kw1gE2C.', -- 'adminpassword'
                'US',
                '1234567890'
            )
        SQL);

        // Link admin user to ROLE_ADMIN
        $this->addSql('
            INSERT INTO user_roles (user_id, role_id)
            VALUES (
                (SELECT id FROM user WHERE email = "admin@example.com" LIMIT 1),
                (SELECT id FROM role WHERE role = "ROLE_ADMIN" LIMIT 1)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE role');
    }
}
