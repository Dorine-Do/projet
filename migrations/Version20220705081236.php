<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705081236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_module_instructor (module_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_A4196840AFC2B591 (module_id), INDEX IDX_A41968408C4FC193 (instructor_id), PRIMARY KEY(module_id, instructor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_module_qcm (module_id INT NOT NULL, qcm_id INT NOT NULL, INDEX IDX_3BD2F700AFC2B591 (module_id), INDEX IDX_3BD2F700FF6241A6 (qcm_id), PRIMARY KEY(module_id, qcm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_qcm_question (qcm_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_572B6C8DFF6241A6 (qcm_id), INDEX IDX_572B6C8D1E27F6BF (question_id), PRIMARY KEY(qcm_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_session_instructor (session_id INT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_D16A4886613FECDF (session_id), INDEX IDX_D16A48868C4FC193 (instructor_id), PRIMARY KEY(session_id, instructor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_A4196840AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_instructor ADD CONSTRAINT FK_A41968408C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_3BD2F700AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_module_qcm ADD CONSTRAINT FK_3BD2F700FF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_572B6C8DFF6241A6 FOREIGN KEY (qcm_id) REFERENCES qcm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_qcm_question ADD CONSTRAINT FK_572B6C8D1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_D16A4886613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_session_instructor ADD CONSTRAINT FK_D16A48868C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student CHANGE badges badges JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE link_module_instructor');
        $this->addSql('DROP TABLE link_module_qcm');
        $this->addSql('DROP TABLE link_qcm_question');
        $this->addSql('DROP TABLE link_session_instructor');
        $this->addSql('ALTER TABLE student CHANGE badges badges JSON DEFAULT NULL');
    }
}
