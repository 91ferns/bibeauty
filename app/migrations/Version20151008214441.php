<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008214441 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_service_type DROP CONSTRAINT fk_8013f944dedcbb4e');
        $this->addSql('ALTER TABLE app_services DROP CONSTRAINT fk_7bb281c4ac8de0f');
        $this->addSql('ALTER TABLE app_subscriptions DROP CONSTRAINT fk_9e0ab7aded5ca9e6');
        $this->addSql('ALTER TABLE app_treatment_availability_sets DROP CONSTRAINT fk_16840eb9ed5ca9e6');
        $this->addSql('ALTER TABLE app_bookings DROP CONSTRAINT fk_72055c98ed5ca9e6');
        $this->addSql('DROP SEQUENCE app_bookings_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_service_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_services_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_service_categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_subscriptions_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_offers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_treatments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_treatment_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_offers (id INT NOT NULL, availability_id INT DEFAULT NULL, user_id INT DEFAULT NULL, business_id INT DEFAULT NULL, treatment_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approval INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3F4569A761778466 ON app_offers (availability_id)');
        $this->addSql('CREATE INDEX IDX_3F4569A7A76ED395 ON app_offers (user_id)');
        $this->addSql('CREATE INDEX IDX_3F4569A7A89DB457 ON app_offers (business_id)');
        $this->addSql('CREATE INDEX IDX_3F4569A7471C0366 ON app_offers (treatment_id)');
        $this->addSql('CREATE TABLE app_treatments (id INT NOT NULL, treatment_category_id INT DEFAULT NULL, business_id INT DEFAULT NULL, therapist_id INT DEFAULT NULL, hours INT NOT NULL, minutes INT NOT NULL, original_price DOUBLE PRECISION NOT NULL, current_price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C5365717F8807B4 ON app_treatments (treatment_category_id)');
        $this->addSql('CREATE INDEX IDX_1C536571A89DB457 ON app_treatments (business_id)');
        $this->addSql('CREATE INDEX IDX_1C53657143E8B094 ON app_treatments (therapist_id)');
        $this->addSql('CREATE TABLE app_treatment_categories (id INT NOT NULL, parent_category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C2DD73AA796A8F92 ON app_treatment_categories (parent_category_id)');
        $this->addSql('ALTER TABLE app_offers ADD CONSTRAINT FK_3F4569A761778466 FOREIGN KEY (availability_id) REFERENCES app_treatment_availability_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_offers ADD CONSTRAINT FK_3F4569A7A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_offers ADD CONSTRAINT FK_3F4569A7A89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_offers ADD CONSTRAINT FK_3F4569A7471C0366 FOREIGN KEY (treatment_id) REFERENCES app_treatments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_treatments ADD CONSTRAINT FK_1C5365717F8807B4 FOREIGN KEY (treatment_category_id) REFERENCES app_treatment_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_treatments ADD CONSTRAINT FK_1C536571A89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_treatments ADD CONSTRAINT FK_1C53657143E8B094 FOREIGN KEY (therapist_id) REFERENCES app_therapists (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_treatment_categories ADD CONSTRAINT FK_C2DD73AA796A8F92 FOREIGN KEY (parent_category_id) REFERENCES app_treatment_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE app_subscriptions');
        $this->addSql('DROP TABLE app_bookings');
        $this->addSql('DROP TABLE app_service_categories');
        $this->addSql('DROP TABLE app_service_type');
        $this->addSql('DROP TABLE app_services');
        $this->addSql('ALTER TABLE app_addresses DROP phone');
        $this->addSql('ALTER TABLE app_businesses ADD landline VARCHAR(14) NOT NULL');
        $this->addSql('ALTER TABLE app_businesses ADD mobile VARCHAR(14) NOT NULL');
        $this->addSql('ALTER TABLE app_recurring_appointments DROP CONSTRAINT fk_899b42e071380cf6');
        $this->addSql('DROP INDEX idx_899b42e071380cf6');
        $this->addSql('ALTER TABLE app_recurring_appointments RENAME COLUMN availabilityid TO availability_id');
        $this->addSql('ALTER TABLE app_recurring_appointments ADD CONSTRAINT FK_899B42E061778466 FOREIGN KEY (availability_id) REFERENCES app_treatment_availability_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_899B42E061778466 ON app_recurring_appointments (availability_id)');
        $this->addSql('DROP INDEX idx_16840eb9ed5ca9e6');
        $this->addSql('ALTER TABLE app_treatment_availability_sets RENAME COLUMN service_id TO treatment_id');
        $this->addSql('ALTER TABLE app_treatment_availability_sets ADD CONSTRAINT FK_16840EB9471C0366 FOREIGN KEY (treatment_id) REFERENCES app_treatments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_16840EB9471C0366 ON app_treatment_availability_sets (treatment_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_offers DROP CONSTRAINT FK_3F4569A7471C0366');
        $this->addSql('ALTER TABLE app_treatment_availability_sets DROP CONSTRAINT FK_16840EB9471C0366');
        $this->addSql('ALTER TABLE app_treatments DROP CONSTRAINT FK_1C5365717F8807B4');
        $this->addSql('ALTER TABLE app_treatment_categories DROP CONSTRAINT FK_C2DD73AA796A8F92');
        $this->addSql('DROP SEQUENCE app_offers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_treatments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_treatment_categories_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_bookings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_service_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_services_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_service_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_subscriptions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_subscriptions (id INT NOT NULL, user_id INT DEFAULT NULL, service_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9e0ab7aded5ca9e6 ON app_subscriptions (service_id)');
        $this->addSql('CREATE INDEX idx_9e0ab7ada76ed395 ON app_subscriptions (user_id)');
        $this->addSql('CREATE TABLE app_bookings (id INT NOT NULL, availability_id INT DEFAULT NULL, user_id INT DEFAULT NULL, business_id INT DEFAULT NULL, service_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approval INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_72055c9861778466 ON app_bookings (availability_id)');
        $this->addSql('CREATE INDEX idx_72055c98ed5ca9e6 ON app_bookings (service_id)');
        $this->addSql('CREATE INDEX idx_72055c98a76ed395 ON app_bookings (user_id)');
        $this->addSql('CREATE INDEX idx_72055c98a89db457 ON app_bookings (business_id)');
        $this->addSql('CREATE TABLE app_service_categories (id INT NOT NULL, business_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ccb3b4a4a89db457 ON app_service_categories (business_id)');
        $this->addSql('CREATE TABLE app_service_type (id INT NOT NULL, service_category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8013f944dedcbb4e ON app_service_type (service_category_id)');
        $this->addSql('CREATE TABLE app_services (id INT NOT NULL, service_type_id INT DEFAULT NULL, business_id INT DEFAULT NULL, therapist_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, hours INT NOT NULL, minutes INT NOT NULL, original_price DOUBLE PRECISION NOT NULL, current_price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7bb281c443e8b094 ON app_services (therapist_id)');
        $this->addSql('CREATE INDEX idx_7bb281c4ac8de0f ON app_services (service_type_id)');
        $this->addSql('CREATE INDEX idx_7bb281c4a89db457 ON app_services (business_id)');
        $this->addSql('ALTER TABLE app_subscriptions ADD CONSTRAINT fk_9e0ab7ada76ed395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_subscriptions ADD CONSTRAINT fk_9e0ab7aded5ca9e6 FOREIGN KEY (service_id) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT fk_72055c9861778466 FOREIGN KEY (availability_id) REFERENCES app_treatment_availability_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT fk_72055c98a76ed395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT fk_72055c98a89db457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT fk_72055c98ed5ca9e6 FOREIGN KEY (service_id) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_service_categories ADD CONSTRAINT fk_ccb3b4a4a89db457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_service_type ADD CONSTRAINT fk_8013f944dedcbb4e FOREIGN KEY (service_category_id) REFERENCES app_service_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT fk_7bb281c4ac8de0f FOREIGN KEY (service_type_id) REFERENCES app_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT fk_7bb281c4a89db457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT fk_7bb281c443e8b094 FOREIGN KEY (therapist_id) REFERENCES app_therapists (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE app_offers');
        $this->addSql('DROP TABLE app_treatments');
        $this->addSql('DROP TABLE app_treatment_categories');
        $this->addSql('ALTER TABLE app_addresses ADD phone VARCHAR(14) NOT NULL');
        $this->addSql('DROP INDEX IDX_16840EB9471C0366');
        $this->addSql('ALTER TABLE app_treatment_availability_sets RENAME COLUMN treatment_id TO service_id');
        $this->addSql('ALTER TABLE app_treatment_availability_sets ADD CONSTRAINT fk_16840eb9ed5ca9e6 FOREIGN KEY (service_id) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_16840eb9ed5ca9e6 ON app_treatment_availability_sets (service_id)');
        $this->addSql('ALTER TABLE app_recurring_appointments DROP CONSTRAINT FK_899B42E061778466');
        $this->addSql('DROP INDEX IDX_899B42E061778466');
        $this->addSql('ALTER TABLE app_recurring_appointments RENAME COLUMN availability_id TO availabilityid');
        $this->addSql('ALTER TABLE app_recurring_appointments ADD CONSTRAINT fk_899b42e071380cf6 FOREIGN KEY (availabilityid) REFERENCES app_treatment_availability_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_899b42e071380cf6 ON app_recurring_appointments (availabilityid)');
        $this->addSql('ALTER TABLE app_businesses DROP landline');
        $this->addSql('ALTER TABLE app_businesses DROP mobile');
    }
}
