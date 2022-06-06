<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513133204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE module ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP id_tools');
        $this->addSql('ALTER TABLE question DROP difficulty_points');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE module ADD id_tools INT UNSIGNED NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE question ADD difficulty_points TINYINT(1) DEFAULT NULL');
    }
}
