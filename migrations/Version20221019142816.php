<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019142816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badges (id INT AUTO_INCREMENT NOT NULL, module_group_name VARCHAR(255) NOT NULL, img_file VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bug_report (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F6F2DC7AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cookie (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cookie VARCHAR(32) DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8AE0BA66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_instructor_session_module (instructor_id INT NOT NULL, session_id INT NOT NULL, module_id INT NOT NULL, INDEX IDX_DA16236F8C4FC193 (instructor_id), INDEX IDX_DA16236F613FECDF (session_id), INDEX IDX_DA16236FAFC2B591 (module_id), PRIMARY KEY(instructor_id, session_id, module_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_module (session_id INT NOT NULL, module_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_773BE832613FECDF (session_id), INDEX IDX_773BE832AFC2B591 (module_id), PRIMARY KEY(session_id, module_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_student (session_id INT NOT NULL, student_id INT NOT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_4A3A4987613FECDF (session_id), INDEX IDX_4A3A4987CB944F1A (student_id), PRIMARY KEY(session_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, log LONGTEXT NOT NULL, level SMALLINT NOT NULL, path VARCHAR(150) NOT NULL, latency VARCHAR(150) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, weeks SMALLINT NOT NULL, title VARCHAR(50) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, wording LONGTEXT NOT NULL, is_correct_answer TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_BFE594721E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qcm (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(75) NOT NULL, difficulty SMALLINT NOT NULL, is_official TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, is_public TINYINT(1) NOT NULL, questions_cache LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_D7A1FEF4AFC2B591 (module_id), INDEX IDX_D7A1FEF4F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_qcm_question (qcm_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_A584EC4FFF6241A6 (qcm_id), INDEX IDX_A584EC4F1E27F6BF (question_id), PRIMARY KEY(qcm_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qcm_instance (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, qcm_id INT NOT NULL, distributed_by_id INT NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_A3EC941DCB944F1A (student_id), INDEX IDX_A3EC941DFF6241A6 (qcm_id), INDEX IDX_A3EC941D65F14916 (distributed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, author_id INT NOT NULL, wording LONGTEXT NOT NULL, is_mandatory TINYINT(1) NOT NULL, is_official TINYINT(1) NOT NULL, difficulty SMALLINT NOT NULL, is_multiple TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_enabled TINYINT(1) NOT NULL, explanation LONGTEXT DEFAULT NULL, INDEX IDX_B6F7494EAFC2B591 (module_id), INDEX IDX_B6F7494EF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, qcm_instance_id INT NOT NULL, submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, answers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', score SMALLINT NOT NULL, student_comment LONGTEXT DEFAULT NULL, instructor_comment LONGTEXT DEFAULT NULL, is_first_try TINYINT(1) NOT NULL, level SMALLINT DEFAULT NULL, UNIQUE INDEX UNIQ_136AC113CE2E14FD (qcm_instance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, school_year SMALLINT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, birth_date DATETIME DEFAULT NULL, moodle_id INT NOT NULL, suivi_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, discr VARCHAR(255) NOT NULL, phone VARCHAR(12) DEFAULT NULL, is_referent TINYINT(1) DEFAULT NULL, badges LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cookie ADD CONSTRAINT FK_8AE0BA66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE link_instructor_session_module ADD CONSTRAINT FK_DA16236F8C4FC193 FOREIGN KEY (instructor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE link_instructor_session_module ADD CONSTRAINT FK_DA16236F613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_instructor_session_module ADD CONSTRAINT FK_DA16236FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_session_module ADD CONSTRAINT FK_773BE832613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_session_module ADD CONSTRAINT FK_773BE832AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_session_student ADD CONSTRAINT FK_4A3A4987613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_session_student ADD CONSTRAINT FK_4A3A4987CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF4AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF4F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4FFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4F1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qcm_instance ADD CONSTRAINT FK_A3EC941DCB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE qcm_instance ADD CONSTRAINT FK_A3EC941DFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id)');
        $this->addSql('ALTER TABLE qcm_instance ADD CONSTRAINT FK_A3EC941D65F14916 FOREIGN KEY (distributed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113CE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP FOREIGN KEY FK_F6F2DC7AA76ED395');
        $this->addSql('ALTER TABLE cookie DROP FOREIGN KEY FK_8AE0BA66A76ED395');
        $this->addSql('ALTER TABLE link_instructor_session_module DROP FOREIGN KEY FK_DA16236F8C4FC193');
        $this->addSql('ALTER TABLE link_instructor_session_module DROP FOREIGN KEY FK_DA16236F613FECDF');
        $this->addSql('ALTER TABLE link_instructor_session_module DROP FOREIGN KEY FK_DA16236FAFC2B591');
        $this->addSql('ALTER TABLE link_session_module DROP FOREIGN KEY FK_773BE832613FECDF');
        $this->addSql('ALTER TABLE link_session_module DROP FOREIGN KEY FK_773BE832AFC2B591');
        $this->addSql('ALTER TABLE link_session_student DROP FOREIGN KEY FK_4A3A4987613FECDF');
        $this->addSql('ALTER TABLE link_session_student DROP FOREIGN KEY FK_4A3A4987CB944F1A');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE594721E27F6BF');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF4AFC2B591');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF4F675F31B');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4FFF6241A6');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4F1E27F6BF');
        $this->addSql('ALTER TABLE qcm_instance DROP FOREIGN KEY FK_A3EC941DCB944F1A');
        $this->addSql('ALTER TABLE qcm_instance DROP FOREIGN KEY FK_A3EC941DFF6241A6');
        $this->addSql('ALTER TABLE qcm_instance DROP FOREIGN KEY FK_A3EC941D65F14916');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EAFC2B591');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EF675F31B');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113CE2E14FD');
        $this->addSql('DROP TABLE badges');
        $this->addSql('DROP TABLE bug_report');
        $this->addSql('DROP TABLE cookie');
        $this->addSql('DROP TABLE link_instructor_session_module');
        $this->addSql('DROP TABLE link_session_module');
        $this->addSql('DROP TABLE link_session_student');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE qcm');
        $this->addSql('DROP TABLE link_qcm_question');
        $this->addSql('DROP TABLE qcm_instance');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
