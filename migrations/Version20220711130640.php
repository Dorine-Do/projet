<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711130640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE link_module_qcm');
        $this->addSql('ALTER TABLE qcm ADD module_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE qcm ADD CONSTRAINT FK_D7A1FEF4AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('CREATE INDEX IDX_D7A1FEF4AFC2B591 ON qcm (module_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_module_qcm (module_id INT NOT NULL, qcm_id INT NOT NULL, INDEX IDX_6C5837F5AFC2B591 (module_id), INDEX IDX_6C5837F5FF6241A6 (qcm_id), PRIMARY KEY(module_id, qcm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE qcm DROP FOREIGN KEY FK_D7A1FEF4AFC2B591');
        $this->addSql('DROP INDEX IDX_D7A1FEF4AFC2B591 ON qcm');
        $this->addSql('ALTER TABLE qcm DROP module_id');
    }
}
