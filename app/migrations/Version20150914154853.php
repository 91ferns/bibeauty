<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150914154853 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE app_services_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_service_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_subscriptions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_services (id INT NOT NULL, service_category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, hours INT NOT NULL, minutes INT NOT NULL, original_price DOUBLE PRECISION NOT NULL, current_price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7BB281C4DEDCBB4E ON app_services (service_category_id)');
        $this->addSql('CREATE TABLE app_service_categories (id INT NOT NULL, business_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CCB3B4A4A89DB457 ON app_service_categories (business_id)');
        $this->addSql('CREATE TABLE app_subscriptions (id INT NOT NULL, user_id INT DEFAULT NULL, service_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E0AB7ADA76ED395 ON app_subscriptions (user_id)');
        $this->addSql('CREATE INDEX IDX_9E0AB7ADED5CA9E6 ON app_subscriptions (service_id)');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT FK_7BB281C4DEDCBB4E FOREIGN KEY (service_category_id) REFERENCES app_service_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_service_categories ADD CONSTRAINT FK_CCB3B4A4A89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_subscriptions ADD CONSTRAINT FK_9E0AB7ADA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_subscriptions ADD CONSTRAINT FK_9E0AB7ADED5CA9E6 FOREIGN KEY (service_id) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_businesses RENAME COLUMN yelp_id TO yelp_link');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_subscriptions DROP CONSTRAINT FK_9E0AB7ADED5CA9E6');
        $this->addSql('ALTER TABLE app_services DROP CONSTRAINT FK_7BB281C4DEDCBB4E');
        $this->addSql('DROP SEQUENCE app_services_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_service_categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_subscriptions_id_seq CASCADE');
        $this->addSql('DROP TABLE app_services');
        $this->addSql('DROP TABLE app_service_categories');
        $this->addSql('DROP TABLE app_subscriptions');
        $this->addSql('ALTER TABLE app_businesses RENAME COLUMN yelp_link TO yelp_id');
    }
}
