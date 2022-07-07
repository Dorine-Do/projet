<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706163226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE link_instructor_module');
        $this->addSql('DROP TABLE link_instructor_session');
        $this->addSql('ALTER TABLE instructor ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE password password VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE link_module_instructor DROP FOREIGN KEY FK_A41968408C4FC193');
        $this->addSql('ALTER TABLE link_module_instructor DROP FOREIGN KEY FK_A4196840AFC2B591');
        $this->addSql('DROP INDEX idx_a4196840afc2b591 ON link_module_instructor');
        $this->addSql('CREATE INDEX IDX_2E3BFCA2AFC2B591 ON link_module_instructor (module_id)');
        $this->addSql('DROP INDEX idx_a41968408c4fc193 ON link_module_instructor');
        $this->addSql('CREATE INDEX IDX_2E3BFCA28C4FC193 ON link_module_instructor (instructor_id)');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_A41968408C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_A4196840AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_qcm DROP FOREIGN KEY FK_3BD2F700AFC2B591');
        $this->addSql('ALTER TABLE link_module_qcm DROP FOREIGN KEY FK_3BD2F700FF6241A6');
        $this->addSql('DROP INDEX idx_3bd2f700afc2b591 ON link_module_qcm');
        $this->addSql('CREATE INDEX IDX_6C5837F5AFC2B591 ON link_module_qcm (module_id)');
        $this->addSql('DROP INDEX idx_3bd2f700ff6241a6 ON link_module_qcm');
        $this->addSql('CREATE INDEX IDX_6C5837F5FF6241A6 ON link_module_qcm (qcm_id)');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_3BD2F700AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_3BD2F700FF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_572B6C8D1E27F6BF');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_572B6C8DFF6241A6');
        $this->addSql('DROP INDEX idx_572b6c8dff6241a6 ON link_qcm_question');
        $this->addSql('CREATE INDEX IDX_A584EC4FFF6241A6 ON link_qcm_question (qcm_id)');
        $this->addSql('DROP INDEX idx_572b6c8d1e27f6bf ON link_qcm_question');
        $this->addSql('CREATE INDEX IDX_A584EC4F1E27F6BF ON link_qcm_question (question_id)');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_572B6C8D1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_572B6C8DFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE link_session_instructor DROP FOREIGN KEY FK_D16A4886613FECDF');
        $this->addSql('ALTER TABLE link_session_instructor DROP FOREIGN KEY FK_D16A48868C4FC193');
        $this->addSql('DROP INDEX idx_d16a4886613fecdf ON link_session_instructor');
        $this->addSql('CREATE INDEX IDX_9FE4E946613FECDF ON link_session_instructor (session_id)');
        $this->addSql('DROP INDEX idx_d16a48868c4fc193 ON link_session_instructor');
        $this->addSql('CREATE INDEX IDX_9FE4E9468C4FC193 ON link_session_instructor (instructor_id)');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_D16A4886613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_D16A48868C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_student_qcm_instance DROP FOREIGN KEY FK_3A3225AACB944F1A');
        $this->addSql('ALTER TABLE link_student_qcm_instance DROP FOREIGN KEY FK_3A3225AACE2E14FD');
        $this->addSql('DROP INDEX idx_3a3225aacb944f1a ON link_student_qcm_instance');
        $this->addSql('CREATE INDEX IDX_50192F14CB944F1A ON link_student_qcm_instance (student_id)');
        $this->addSql('DROP INDEX idx_3a3225aace2e14fd ON link_student_qcm_instance');
        $this->addSql('CREATE INDEX IDX_50192F14CE2E14FD ON link_student_qcm_instance (qcm_instance_id)');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_3A3225AACB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_3A3225AACE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_instructor_module (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_F9BE6D96AFC2B591 (module_id), INDEX IDX_F9BE6D968C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE link_instructor_session (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_FC68D1148C4FC193 (instructor_id), INDEX IDX_FC68D114613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE link_instructor_module ADD CONSTRAINT FK_F9BE6D968C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE link_instructor_module ADD CONSTRAINT FK_F9BE6D96AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE link_instructor_session ADD CONSTRAINT FK_FC68D114613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE link_instructor_session ADD CONSTRAINT FK_FC68D1148C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE instructor DROP roles, CHANGE password password VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE link_module_instructor DROP FOREIGN KEY FK_2E3BFCA2AFC2B591');
        $this->addSql('ALTER TABLE link_module_instructor DROP FOREIGN KEY FK_2E3BFCA28C4FC193');
        $this->addSql('DROP INDEX idx_2e3bfca2afc2b591 ON link_module_instructor');
        $this->addSql('CREATE INDEX IDX_A4196840AFC2B591 ON link_module_instructor (module_id)');
        $this->addSql('DROP INDEX idx_2e3bfca28c4fc193 ON link_module_instructor');
        $this->addSql('CREATE INDEX IDX_A41968408C4FC193 ON link_module_instructor (instructor_id)');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_2E3BFCA2AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_2E3BFCA28C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_qcm DROP FOREIGN KEY FK_6C5837F5AFC2B591');
        $this->addSql('ALTER TABLE link_module_qcm DROP FOREIGN KEY FK_6C5837F5FF6241A6');
        $this->addSql('DROP INDEX idx_6c5837f5afc2b591 ON link_module_qcm');
        $this->addSql('CREATE INDEX IDX_3BD2F700AFC2B591 ON link_module_qcm (module_id)');
        $this->addSql('DROP INDEX idx_6c5837f5ff6241a6 ON link_module_qcm');
        $this->addSql('CREATE INDEX IDX_3BD2F700FF6241A6 ON link_module_qcm (qcm_id)');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_6C5837F5AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_6C5837F5FF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4FFF6241A6');
        $this->addSql('ALTER TABLE link_qcm_question DROP FOREIGN KEY FK_A584EC4F1E27F6BF');
        $this->addSql('DROP INDEX idx_a584ec4fff6241a6 ON link_qcm_question');
        $this->addSql('CREATE INDEX IDX_572B6C8DFF6241A6 ON link_qcm_question (qcm_id)');
        $this->addSql('DROP INDEX idx_a584ec4f1e27f6bf ON link_qcm_question');
        $this->addSql('CREATE INDEX IDX_572B6C8D1E27F6BF ON link_qcm_question (question_id)');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4FFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_A584EC4F1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_instructor DROP FOREIGN KEY FK_9FE4E946613FECDF');
        $this->addSql('ALTER TABLE link_session_instructor DROP FOREIGN KEY FK_9FE4E9468C4FC193');
        $this->addSql('DROP INDEX idx_9fe4e946613fecdf ON link_session_instructor');
        $this->addSql('CREATE INDEX IDX_D16A4886613FECDF ON link_session_instructor (session_id)');
        $this->addSql('DROP INDEX idx_9fe4e9468c4fc193 ON link_session_instructor');
        $this->addSql('CREATE INDEX IDX_D16A48868C4FC193 ON link_session_instructor (instructor_id)');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_9FE4E946613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_9FE4E9468C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_student_qcm_instance DROP FOREIGN KEY FK_50192F14CB944F1A');
        $this->addSql('ALTER TABLE link_student_qcm_instance DROP FOREIGN KEY FK_50192F14CE2E14FD');
        $this->addSql('DROP INDEX idx_50192f14cb944f1a ON link_student_qcm_instance');
        $this->addSql('CREATE INDEX IDX_3A3225AACB944F1A ON link_student_qcm_instance (student_id)');
        $this->addSql('DROP INDEX idx_50192f14ce2e14fd ON link_student_qcm_instance');
        $this->addSql('CREATE INDEX IDX_3A3225AACE2E14FD ON link_student_qcm_instance (qcm_instance_id)');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_50192F14CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_student_qcm_instance ADD CONSTRAINT FK_50192F14CE2E14FD FOREIGN KEY (qcm_instance_id) REFERENCES qcm_instance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session CHANGE name name VARCHAR(10) NOT NULL');
    }
}
