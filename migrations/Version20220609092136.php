<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220609092136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin MODIFY id_tools INT NOT NULL');
        $this->addSql('ALTER TABLE admin DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE admin CHANGE id_tools id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE admin ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE student CHANGE badges badges JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE admin DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE admin CHANGE id id_tools INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE admin ADD PRIMARY KEY (id_tools)');
        $this->addSql('ALTER TABLE student CHANGE badges badges JSON DEFAULT NULL');
    }
}
