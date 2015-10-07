<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007155408 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE app_bookings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_recurring_appointments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_service_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_therapists_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_treatment_availability_sets_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_bookings (id INT NOT NULL, availability_id INT DEFAULT NULL, user_id INT DEFAULT NULL, business_id INT DEFAULT NULL, service_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approval INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_72055C9861778466 ON app_bookings (availability_id)');
        $this->addSql('CREATE INDEX IDX_72055C98A76ED395 ON app_bookings (user_id)');
        $this->addSql('CREATE INDEX IDX_72055C98A89DB457 ON app_bookings (business_id)');
        $this->addSql('CREATE INDEX IDX_72055C98ED5CA9E6 ON app_bookings (service_id)');
        $this->addSql('CREATE TABLE app_recurring_appointments (id INT NOT NULL, date DATE NOT NULL, time TIME(0) WITHOUT TIME ZONE NOT NULL, availabilityId INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_899B42E071380CF6 ON app_recurring_appointments (availabilityId)');
        $this->addSql('CREATE TABLE app_service_type (id INT NOT NULL, service_category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8013F944DEDCBB4E ON app_service_type (service_category_id)');
        $this->addSql('CREATE TABLE app_therapists (id INT NOT NULL, business_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(20) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(150) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E6C8644DA89DB457 ON app_therapists (business_id)');
        $this->addSql('CREATE TABLE app_treatment_availability_sets (id INT NOT NULL, date DATE NOT NULL, time TIME(0) WITHOUT TIME ZONE NOT NULL, is_open BOOLEAN NOT NULL, recurring BOOLEAN DEFAULT \'false\' NOT NULL, serviceId INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_16840EB989697FA8 ON app_treatment_availability_sets (serviceId)');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT FK_72055C9861778466 FOREIGN KEY (availability_id) REFERENCES app_treatment_availability_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT FK_72055C98A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT FK_72055C98A89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_bookings ADD CONSTRAINT FK_72055C98ED5CA9E6 FOREIGN KEY (service_id) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_recurring_appointments ADD CONSTRAINT FK_899B42E071380CF6 FOREIGN KEY (availabilityId) REFERENCES app_treatment_availability_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_service_type ADD CONSTRAINT FK_8013F944DEDCBB4E FOREIGN KEY (service_category_id) REFERENCES app_service_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_therapists ADD CONSTRAINT FK_E6C8644DA89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_treatment_availability_sets ADD CONSTRAINT FK_16840EB989697FA8 FOREIGN KEY (serviceId) REFERENCES app_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_services DROP CONSTRAINT fk_7bb281c4dedcbb4e');
        $this->addSql('DROP INDEX idx_7bb281c4dedcbb4e');
        $this->addSql('ALTER TABLE app_services ADD business_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_services ADD therapist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_services ALTER label SET DEFAULT \'N/A\'');
        $this->addSql('ALTER TABLE app_services RENAME COLUMN service_category_id TO service_type_id');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT FK_7BB281C4AC8DE0F FOREIGN KEY (service_type_id) REFERENCES app_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT FK_7BB281C4A89DB457 FOREIGN KEY (business_id) REFERENCES app_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT FK_7BB281C443E8B094 FOREIGN KEY (therapist_id) REFERENCES app_therapists (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7BB281C4AC8DE0F ON app_services (service_type_id)');
        $this->addSql('CREATE INDEX IDX_7BB281C4A89DB457 ON app_services (business_id)');
        $this->addSql('CREATE INDEX IDX_7BB281C443E8B094 ON app_services (therapist_id)');
        $this->addSql('DROP INDEX uniq_c2502824a9d1c132');
        $this->addSql('DROP INDEX uniq_c2502824c808ba5a');
        $this->addSql('ALTER TABLE app_users ADD super_admin BOOLEAN NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_services DROP CONSTRAINT FK_7BB281C4AC8DE0F');
        $this->addSql('ALTER TABLE app_services DROP CONSTRAINT FK_7BB281C443E8B094');
        $this->addSql('ALTER TABLE app_bookings DROP CONSTRAINT FK_72055C9861778466');
        $this->addSql('ALTER TABLE app_recurring_appointments DROP CONSTRAINT FK_899B42E071380CF6');
        $this->addSql('DROP SEQUENCE app_bookings_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_recurring_appointments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_service_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_therapists_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_treatment_availability_sets_id_seq CASCADE');
        $this->addSql('DROP TABLE app_bookings');
        $this->addSql('DROP TABLE app_recurring_appointments');
        $this->addSql('DROP TABLE app_service_type');
        $this->addSql('DROP TABLE app_therapists');
        $this->addSql('DROP TABLE app_treatment_availability_sets');
        $this->addSql('ALTER TABLE app_users DROP super_admin');
        $this->addSql('CREATE UNIQUE INDEX uniq_c2502824a9d1c132 ON app_users (first_name)');
        $this->addSql('CREATE UNIQUE INDEX uniq_c2502824c808ba5a ON app_users (last_name)');
        $this->addSql('DROP INDEX IDX_7BB281C4AC8DE0F');
        $this->addSql('DROP INDEX IDX_7BB281C4A89DB457');
        $this->addSql('DROP INDEX IDX_7BB281C443E8B094');
        $this->addSql('ALTER TABLE app_services ADD service_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_services DROP service_type_id');
        $this->addSql('ALTER TABLE app_services DROP business_id');
        $this->addSql('ALTER TABLE app_services DROP therapist_id');
        $this->addSql('ALTER TABLE app_services ALTER label DROP DEFAULT');
        $this->addSql('ALTER TABLE app_services ADD CONSTRAINT fk_7bb281c4dedcbb4e FOREIGN KEY (service_category_id) REFERENCES app_service_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7bb281c4dedcbb4e ON app_services (service_category_id)');
    }
}
