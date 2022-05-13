<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510151254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id_tools INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id_tools)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instructor (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, birth_date DATETIME NOT NULL, phone_number VARCHAR(12) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_class_module (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, session_id INT NOT NULL, class_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_31D7E452AFC2B591 (module_id), INDEX IDX_31D7E452613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_class_student (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, session_id INT NOT NULL, class_id INT NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_7CEC4D3CB944F1A (student_id), INDEX IDX_7CEC4D3613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_instructor_class (id INT AUTO_INCREMENT NOT NULL, instructor_id INT NOT NULL, session_id INT NOT NULL, class_id INT NOT NULL, INDEX IDX_78C4F0C8613FECDF (session_id), INDEX IDX_78C4F0C88C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_instructor_module (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_F9BE6D968C4FC193 (instructor_id), INDEX IDX_F9BE6D96AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_qcm_question (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, qcm_id INT NOT NULL, INDEX IDX_A584EC4F1E27F6BF (question_id), INDEX IDX_A584EC4FFF6241A6 (qcm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, log LONGTEXT NOT NULL, level TINYINT(1) NOT NULL, path VARCHAR(150) NOT NULL, latency VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, number_of_weeks TINYINT(1) NOT NULL, title VARCHAR(50) NOT NULL, badges VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, wording LONGTEXT NOT NULL, is_correct TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BFE594721E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qcm (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, questions_answers JSON NOT NULL, enabled TINYINT(1) NOT NULL, name VARCHAR(75) NOT NULL, difficulty VARCHAR(255) NOT NULL, is_official TINYINT(1) NOT NULL, author_id INT DEFAULT NULL, public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D7A1FEF4AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qcm_instance (id INT AUTO_INCREMENT NOT NULL, qcm_id INT NOT NULL, questions_answers JSON NOT NULL, enabled TINYINT(1) NOT NULL, name VARCHAR(75) NOT NULL, release_date DATETIME NOT NULL, end_date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A3EC941DFF6241A6 (qcm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, id_author INT DEFAULT NULL, wording LONGTEXT NOT NULL, is_mandatory TINYINT(1) NOT NULL, is_official TINYINT(1) NOT NULL, difficulty VARCHAR(255) NOT NULL, response_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6F7494EAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, qcm_instance_id INT NOT NULL, level VARCHAR(255) NOT NULL, answers JSON NOT NULL, total_score DOUBLE PRECISION NOT NULL, instructor_comment VARCHAR(500) DEFAULT NULL, student_comment VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_136AC113CE2E14FD (qcm_instance_id), INDEX IDX_136AC113CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, school_year TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, id_module INT NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, birth_date DATETIME NOT NULL, badges JSON NOT NULL, mail_3wa VARCHAR(45) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_class_module ADD CONSTRAINT FK_31D7E452AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_class_module ADD CONSTRAINT FK_31D7E452613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_class_student ADD CONSTRAINT FK_7CEC4D3CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE link_class_student ADD CONSTRAINT FK_7CEC4D3613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_instructor_class ADD CONSTRAINT FK_78C4F0C8613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_instructor_class ADD CONSTRAINT FK_78C4F0C88C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE link_instructor_module ADD CONSTRAINT FK_F9BE6D968C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE link_instructor_module ADD CONSTRAINT FK_F9BE6D96AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4F1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4FFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF4AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE qcm_instance ADD CONSTRAINT FK_A3EC941DFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113CE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_instructor_class DROP FOREIGN KEY FK_78C4F0C88C4FC193');
        $this->addSql('ALTER TABLE link_instructor_module DROP FOREIGN KEY FK_F9BE6D968C4FC193');
        $this->addSql('ALTER TABLE link_class_module DROP FOREIGN KEY FK_31D7E452AFC2B591');
        $this->addSql('ALTER TABLE link_instructor_module DROP FOREIGN KEY FK_F9BE6D96AFC2B591');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF4AFC2B591');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EAFC2B591');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4FFF6241A6');
        $this->addSql('ALTER TABLE qcm_instance DROP FOREIGN KEY FK_A3EC941DFF6241A6');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113CE2E14FD');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4F1E27F6BF');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE594721E27F6BF');
        $this->addSql('ALTER TABLE link_class_module DROP FOREIGN KEY FK_31D7E452613FECDF');
        $this->addSql('ALTER TABLE link_class_student DROP FOREIGN KEY FK_7CEC4D3613FECDF');
        $this->addSql('ALTER TABLE link_instructor_class DROP FOREIGN KEY FK_78C4F0C8613FECDF');
        $this->addSql('ALTER TABLE link_class_student DROP FOREIGN KEY FK_7CEC4D3CB944F1A');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113CB944F1A');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE instructor');
        $this->addSql('DROP TABLE link_class_module');
        $this->addSql('DROP TABLE link_class_student');
        $this->addSql('DROP TABLE link_instructor_class');
        $this->addSql('DROP TABLE link_instructor_module');
        $this->addSql('DROP TABLE link_qcm_question');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE qcm');
        $this->addSql('DROP TABLE qcm_instance');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
