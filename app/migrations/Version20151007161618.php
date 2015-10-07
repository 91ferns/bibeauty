<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007161618 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_treatment_availability_sets DROP CONSTRAINT fk_16840eb989697fa8');
        $this->addSql('DROP INDEX idx_16840eb989697fa8');
        $this->addSql('ALTER TABLE app_treatment_availability_sets RENAME COLUMN serviceid TO service_id');
        $this->addSql('ALTER TABLE app_treatment_availability_sets ADD CONSTRAINT FK_16840EB9ED5CA9E6 FOREIGN KEY (service_id) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_16840EB9ED5CA9E6 ON app_treatment_availability_sets (service_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_treatment_availability_sets DROP CONSTRAINT FK_16840EB9ED5CA9E6');
        $this->addSql('DROP INDEX IDX_16840EB9ED5CA9E6');
        $this->addSql('ALTER TABLE app_treatment_availability_sets RENAME COLUMN service_id TO serviceid');
        $this->addSql('ALTER TABLE app_treatment_availability_sets ADD CONSTRAINT fk_16840eb989697fa8 FOREIGN KEY (serviceid) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_16840eb989697fa8 ON app_treatment_availability_sets (serviceid)');
    }
}
