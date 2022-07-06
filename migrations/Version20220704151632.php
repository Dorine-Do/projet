<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704151632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_student_qcm_instance (student_id INT NOT NULL, qcm_instance_id INT NOT NULL, INDEX IDX_3A3225AACB944F1A (student_id), INDEX IDX_3A3225AACE2E14FD (qcm_instance_id), PRIMARY KEY(student_id, qcm_instance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_3A3225AACB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_3A3225AACE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE student_qcm_instance');
    }
}
