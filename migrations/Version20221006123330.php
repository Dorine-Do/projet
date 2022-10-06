<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221006123330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bug_report (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F6F2DC7AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cookie (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cookie VARCHAR(32) DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8AE0BA66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cookie ADD CONSTRAINT FK_8AE0BA66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE link_instructor_session_module MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON link_instructor_session_module');
        $this->addSql('ALTER TABLE link_instructor_session_module DROP id');
        $this->addSql('ALTER TABLE link_instructor_session_module ADD PRIMARY KEY (instructor_id, session_id, module_id)');
        $this->addSql('ALTER TABLE link_session_module MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON link_session_module');
        $this->addSql('ALTER TABLE link_session_module DROP id');
        $this->addSql('ALTER TABLE link_session_module ADD PRIMARY KEY (session_id, module_id)');
        $this->addSql('ALTER TABLE link_session_student MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON link_session_student');
        $this->addSql('ALTER TABLE link_session_student DROP id');
        $this->addSql('ALTER TABLE link_session_student ADD PRIMARY KEY (session_id, student_id)');
        $this->addSql('ALTER TABLE log CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE module CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE proposal CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE qcm ADD distributed_by_id INT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF465F14916 FOREIGN KEY (distributed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D7A1FEF465F14916 ON qcm (distributed_by_id)');
        $this->addSql('ALTER TABLE qcm_instance CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE question CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE result CHANGE submitted_at submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE session CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE user DROP password, DROP email3wa, DROP google_id, CHANGE moodle_id moodle_id INT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE suivi_id suivi_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP FOREIGN KEY FK_F6F2DC7AA76ED395');
        $this->addSql('ALTER TABLE cookie DROP FOREIGN KEY FK_8AE0BA66A76ED395');
        $this->addSql('DROP TABLE bug_report');
        $this->addSql('DROP TABLE cookie');
        $this->addSql('ALTER TABLE link_instructor_session_module ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE link_session_module ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE link_session_student ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE log CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE module CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE proposal CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF465F14916');
        $this->addSql('DROP INDEX IDX_D7A1FEF465F14916 ON qcm');
        $this->addSql('ALTER TABLE qcm DROP distributed_by_id, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE qcm_instance CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE question CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE result CHANGE submitted_at submitted_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE session CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL, ADD email3wa VARCHAR(80) NOT NULL, ADD google_id INT DEFAULT NULL, CHANGE moodle_id moodle_id INT DEFAULT NULL, CHANGE suivi_id suivi_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
