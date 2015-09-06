<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150905204059 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE app_addresses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_attachments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_businesses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_operating_schedules_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_reviews_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_addresses (id INT NOT NULL, street VARCHAR(255) NOT NULL, line2 VARCHAR(255) NOT NULL, city VARCHAR(150) NOT NULL, state VARCHAR(4) NOT NULL, zip VARCHAR(12) NOT NULL, phone VARCHAR(14) NOT NULL, country VARCHAR(50) NOT NULL, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_attachments (id INT NOT NULL, user_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, size INT NOT NULL, mime VARCHAR(255) NOT NULL, processed BOOLEAN NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E21DC6AA76ED395 ON app_attachments (user_id)');
        $this->addSql('CREATE TABLE app_businesses (id INT NOT NULL, address_id INT DEFAULT NULL, operation_id INT DEFAULT NULL, user_id INT DEFAULT NULL, header_attachment_id INT DEFAULT NULL, logo_attachment_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, accepts_cash BOOLEAN DEFAULT NULL, accepts_credit BOOLEAN DEFAULT NULL, yelp_id VARCHAR(255) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BD1FE90F5B7AF75 ON app_businesses (address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BD1FE9044AC3583 ON app_businesses (operation_id)');
        $this->addSql('CREATE INDEX IDX_7BD1FE90A76ED395 ON app_businesses (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BD1FE907735401A ON app_businesses (header_attachment_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BD1FE9081CF3622 ON app_businesses (logo_attachment_id)');
        $this->addSql('CREATE TABLE app_operating_schedules (id INT NOT NULL, monday_start VARCHAR(8) DEFAULT NULL, monday_end VARCHAR(8) DEFAULT NULL, tuesday_start VARCHAR(8) DEFAULT NULL, tuesday_end VARCHAR(8) DEFAULT NULL, wednesday_start VARCHAR(8) DEFAULT NULL, wednesday_end VARCHAR(8) DEFAULT NULL, thursday_start VARCHAR(8) DEFAULT NULL, thursday_end VARCHAR(8) DEFAULT NULL, friday_start VARCHAR(8) DEFAULT NULL, friday_end VARCHAR(8) DEFAULT NULL, saturday_start VARCHAR(8) DEFAULT NULL, saturday_end VARCHAR(8) DEFAULT NULL, sunday_start VARCHAR(8) DEFAULT NULL, sunday_end VARCHAR(8) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_reviews (id INT NOT NULL, user_id INT DEFAULT NULL, business_id INT DEFAULT NULL, rating DOUBLE PRECISION NOT NULL, visited_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, content TEXT NOT NULL, reports INT NOT NULL, verified BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_842D6B42A76ED395 ON app_reviews (user_id)');
        $this->addSql('CREATE INDEX IDX_842D6B42A89DB457 ON app_reviews (business_id)');
        $this->addSql('CREATE TABLE app_users (id INT NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(120) NOT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824E7927C74 ON app_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824A9D1C132 ON app_users (first_name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824C808BA5A ON app_users (last_name)');
        $this->addSql('ALTER TABLE app_attachments ADD CONSTRAINT FK_1E21DC6AA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_businesses ADD CONSTRAINT FK_7BD1FE90F5B7AF75 FOREIGN KEY (address_id) REFERENCES app_addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_businesses ADD CONSTRAINT FK_7BD1FE9044AC3583 FOREIGN KEY (operation_id) REFERENCES app_operating_schedules (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_businesses ADD CONSTRAINT FK_7BD1FE90A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_businesses ADD CONSTRAINT FK_7BD1FE907735401A FOREIGN KEY (header_attachment_id) REFERENCES app_attachments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_businesses ADD CONSTRAINT FK_7BD1FE9081CF3622 FOREIGN KEY (logo_attachment_id) REFERENCES app_attachments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_reviews ADD CONSTRAINT FK_842D6B42A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_reviews ADD CONSTRAINT FK_842D6B42A89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_businesses DROP CONSTRAINT FK_7BD1FE90F5B7AF75');
        $this->addSql('ALTER TABLE app_businesses DROP CONSTRAINT FK_7BD1FE907735401A');
        $this->addSql('ALTER TABLE app_businesses DROP CONSTRAINT FK_7BD1FE9081CF3622');
        $this->addSql('ALTER TABLE app_reviews DROP CONSTRAINT FK_842D6B42A89DB457');
        $this->addSql('ALTER TABLE app_businesses DROP CONSTRAINT FK_7BD1FE9044AC3583');
        $this->addSql('ALTER TABLE app_attachments DROP CONSTRAINT FK_1E21DC6AA76ED395');
        $this->addSql('ALTER TABLE app_businesses DROP CONSTRAINT FK_7BD1FE90A76ED395');
        $this->addSql('ALTER TABLE app_reviews DROP CONSTRAINT FK_842D6B42A76ED395');
        $this->addSql('DROP SEQUENCE app_addresses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_attachments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_businesses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_operating_schedules_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_reviews_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_users_id_seq CASCADE');
        $this->addSql('DROP TABLE app_addresses');
        $this->addSql('DROP TABLE app_attachments');
        $this->addSql('DROP TABLE app_businesses');
        $this->addSql('DROP TABLE app_operating_schedules');
        $this->addSql('DROP TABLE app_reviews');
        $this->addSql('DROP TABLE app_users');
    }
}
