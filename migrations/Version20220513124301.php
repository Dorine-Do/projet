<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513124301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_instructor_session (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_FC68D114613FECDF (session_id), INDEX IDX_FC68D1148C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_module_qcm (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, qcm_id INT NOT NULL, INDEX IDX_6C5837F5AFC2B591 (module_id), INDEX IDX_6C5837F5FF6241A6 (qcm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_module (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, session_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_773BE832AFC2B591 (module_id), INDEX IDX_773BE832613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_student (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, session_id INT NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_4A3A4987CB944F1A (student_id), INDEX IDX_4A3A4987613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_instructor_session ADD CONSTRAINT FK_FC68D114613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_instructor_session ADD CONSTRAINT FK_FC68D1148C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_6C5837F5AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_6C5837F5FF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id)');
        $this->addSql('ALTER TABLE link_session_module ADD CONSTRAINT FK_773BE832AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_session_module ADD CONSTRAINT FK_773BE832613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_session_student ADD CONSTRAINT FK_4A3A4987CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE link_session_student ADD CONSTRAINT FK_4A3A4987613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('DROP TABLE link_class_module');
        $this->addSql('DROP TABLE link_class_student');
        $this->addSql('DROP TABLE link_instructor_class');
        $this->addSql('ALTER TABLE log CHANGE level level SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF4AFC2B591');
        $this->addSql('DROP INDEX IDX_D7A1FEF4AFC2B591 ON qcm');
        $this->addSql('ALTER TABLE qcm DROP module_id');
        $this->addSql('ALTER TABLE question ADD enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE session CHANGE school_year school_year SMALLINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_class_module (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, session_id INT NOT NULL, class_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_31D7E452613FECDF (session_id), INDEX IDX_31D7E452AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE link_class_student (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, session_id INT NOT NULL, class_id INT NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_7CEC4D3613FECDF (session_id), INDEX IDX_7CEC4D3CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE link_instructor_class (id INT AUTO_INCREMENT NOT NULL, instructor_id INT NOT NULL, session_id INT NOT NULL, class_id INT NOT NULL, INDEX IDX_78C4F0C8613FECDF (session_id), INDEX IDX_78C4F0C88C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE link_class_module ADD CONSTRAINT FK_31D7E452613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link_class_module ADD CONSTRAINT FK_31D7E452AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link_class_student ADD CONSTRAINT FK_7CEC4D3613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link_class_student ADD CONSTRAINT FK_7CEC4D3CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link_instructor_class ADD CONSTRAINT FK_78C4F0C8613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE link_instructor_class ADD CONSTRAINT FK_78C4F0C88C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE link_instructor_session');
        $this->addSql('DROP TABLE link_module_qcm');
        $this->addSql('DROP TABLE link_session_module');
        $this->addSql('DROP TABLE link_session_student');
        $this->addSql('ALTER TABLE log CHANGE level level TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE qcm ADD module_id INT NOT NULL');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF4AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D7A1FEF4AFC2B591 ON qcm (module_id)');
        $this->addSql('ALTER TABLE question DROP enabled');
        $this->addSql('ALTER TABLE session CHANGE school_year school_year TINYINT(1) NOT NULL');
    }
}
