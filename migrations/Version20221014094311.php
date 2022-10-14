<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221014094311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding cookie table to store users sessions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `cookies` (
            `id` VARCHAR(128) NOT NULL PRIMARY KEY,
            `user` VARCHAR(180) NOT NULL
            `cookie` BLOB NOT NULL,
            `created_at` INTEGER UNSIGNED NOT NULL,
            `lifetime` MEDIUMINT NOT NULL
        ) COLLATE utf8_bin, ENGINE = InnoDB;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
