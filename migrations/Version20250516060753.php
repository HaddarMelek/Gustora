<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516060753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Vérifie si la colonne is_verified n'existe pas déjà
        $this->addSql(<<<'SQL'
            SET @dbname = DATABASE();
            SET @tablename = 'user';
            SET @columnname = 'is_verified';
            SET @preparedStatement = (SELECT IF(
                (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE (TABLE_SCHEMA = @dbname)
                    AND (TABLE_NAME = @tablename)
                    AND (COLUMN_NAME = @columnname)) > 0,
                'SELECT 1',
                CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` TINYINT(1) NOT NULL DEFAULT 0;')
            ));
            PREPARE alterIfNotExists FROM @preparedStatement;
            EXECUTE alterIfNotExists;
            DEALLOCATE PREPARE alterIfNotExists;
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Ne supprime pas la colonne pour éviter des problèmes
        // Cette méthode est laissée vide intentionnellement
    }
}
