<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713135417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id_tools INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id_tools)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instructor (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, birth_date DATETIME NOT NULL, phone_number VARCHAR(12) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_instructor (instructor_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_9FE4E9468C4FC193 (instructor_id), INDEX IDX_9FE4E946613FECDF (session_id), PRIMARY KEY(instructor_id, session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_module (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, session_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_773BE832AFC2B591 (module_id), INDEX IDX_773BE832613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_student (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, session_id INT NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_4A3A4987CB944F1A (student_id), INDEX IDX_4A3A4987613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, log LONGTEXT NOT NULL, level SMALLINT NOT NULL, path VARCHAR(150) NOT NULL, latency VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, number_of_weeks TINYINT(1) NOT NULL, title VARCHAR(50) NOT NULL, badges VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_module_instructor (module_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_2E3BFCA2AFC2B591 (module_id), INDEX IDX_2E3BFCA28C4FC193 (instructor_id), PRIMARY KEY(module_id, instructor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, wording LONGTEXT NOT NULL, is_correct TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BFE594721E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qcm (id INT AUTO_INCREMENT NOT NULL, module_id INT DEFAULT NULL, questions_answers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', enabled TINYINT(1) NOT NULL, name VARCHAR(75) NOT NULL, difficulty VARCHAR(255) NOT NULL, is_official TINYINT(1) NOT NULL, author_id INT DEFAULT NULL, public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D7A1FEF4AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_qcm_question (qcm_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_A584EC4FFF6241A6 (qcm_id), INDEX IDX_A584EC4F1E27F6BF (question_id), PRIMARY KEY(qcm_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qcm_instance (id INT AUTO_INCREMENT NOT NULL, qcm_id INT NOT NULL, questions_answers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', enabled TINYINT(1) NOT NULL, name VARCHAR(75) NOT NULL, release_date DATETIME NOT NULL, end_date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A3EC941DFF6241A6 (qcm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, id_author INT DEFAULT NULL, wording LONGTEXT NOT NULL, is_mandatory TINYINT(1) NOT NULL, is_official TINYINT(1) NOT NULL, difficulty VARCHAR(255) NOT NULL, response_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_B6F7494EAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, qcm_instance_id INT NOT NULL, student_id INT NOT NULL, level VARCHAR(255) NOT NULL, answers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', total_score DOUBLE PRECISION NOT NULL, instructor_comment VARCHAR(500) DEFAULT NULL, student_comment VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_136AC113CE2E14FD (qcm_instance_id), INDEX IDX_136AC113CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, school_year SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, id_moodle INT NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, birth_date DATETIME NOT NULL, badges LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', mail_3wa VARCHAR(45) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_student_qcm_instance (student_id INT NOT NULL, qcm_instance_id INT NOT NULL, INDEX IDX_50192F14CB944F1A (student_id), INDEX IDX_50192F14CE2E14FD (qcm_instance_id), PRIMARY KEY(student_id, qcm_instance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, id_moodle INT NOT NULL, bith_date DATETIME DEFAULT NULL, mail_3wa VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_9FE4E9468C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_9FE4E946613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_module ADD CONSTRAINT FK_773BE832AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_session_module ADD CONSTRAINT FK_773BE832613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_session_student ADD CONSTRAINT FK_4A3A4987CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE link_session_student ADD CONSTRAINT FK_4A3A4987613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_2E3BFCA2AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_2E3BFCA28C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF4AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4FFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4F1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qcm_instance ADD CONSTRAINT FK_A3EC941DFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113CE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_50192F14CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_50192F14CE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_session_instructor DROP FOREIGN KEY FK_9FE4E9468C4FC193');
        $this->addSql('ALTER TABLE link_module_instructor DROP FOREIGN KEY FK_2E3BFCA28C4FC193');
        $this->addSql('ALTER TABLE link_session_module DROP FOREIGN KEY FK_773BE832AFC2B591');
        $this->addSql('ALTER TABLE link_module_instructor DROP FOREIGN KEY FK_2E3BFCA2AFC2B591');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF4AFC2B591');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EAFC2B591');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4FFF6241A6');
        $this->addSql('ALTER TABLE qcm_instance DROP FOREIGN KEY FK_A3EC941DFF6241A6');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113CE2E14FD');
        $this->addSql('ALTER TABLE link_student_qcm_instance DROP FOREIGN KEY FK_50192F14CE2E14FD');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE594721E27F6BF');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4F1E27F6BF');
        $this->addSql('ALTER TABLE link_session_instructor DROP FOREIGN KEY FK_9FE4E946613FECDF');
        $this->addSql('ALTER TABLE link_session_module DROP FOREIGN KEY FK_773BE832613FECDF');
        $this->addSql('ALTER TABLE link_session_student DROP FOREIGN KEY FK_4A3A4987613FECDF');
        $this->addSql('ALTER TABLE link_session_student DROP FOREIGN KEY FK_4A3A4987CB944F1A');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113CB944F1A');
        $this->addSql('ALTER TABLE link_student_qcm_instance DROP FOREIGN KEY FK_50192F14CB944F1A');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE instructor');
        $this->addSql('DROP TABLE link_session_instructor');
        $this->addSql('DROP TABLE link_session_module');
        $this->addSql('DROP TABLE link_session_student');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE link_module_instructor');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE qcm');
        $this->addSql('DROP TABLE link_qcm_question');
        $this->addSql('DROP TABLE qcm_instance');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE link_student_qcm_instance');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
