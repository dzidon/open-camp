<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208035834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_date_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', simple_id VARCHAR(6) NOT NULL, email VARCHAR(180) NOT NULL, name_first VARCHAR(255) NOT NULL, name_last VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, town VARCHAR(255) NOT NULL, zip VARCHAR(11) NOT NULL, country VARCHAR(2) NOT NULL, business_name VARCHAR(255) DEFAULT NULL, business_cin VARCHAR(32) DEFAULT NULL, business_vat_id VARCHAR(32) DEFAULT NULL, is_draft TINYINT(1) NOT NULL, is_accepted TINYINT(1) DEFAULT NULL, camp_name VARCHAR(255) NOT NULL, deposit DOUBLE PRECISION NOT NULL, price_without_deposit DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL, camp_date_start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', camp_date_end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_A45BDDC1C8FAF195 (simple_id), INDEX IDX_A45BDDC1C8047505 (camp_date_id), INDEX IDX_A45BDDC1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_attachment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', application_camper_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, help VARCHAR(255) DEFAULT NULL, max_size DOUBLE PRECISION NOT NULL, required_type VARCHAR(16) NOT NULL, extensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', extension VARCHAR(255) DEFAULT NULL, priority INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EDCE8573E030ACD (application_id), INDEX IDX_EDCE857F300DF07 (application_camper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_camper (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name_first VARCHAR(255) NOT NULL, name_last VARCHAR(255) NOT NULL, gender VARCHAR(1) NOT NULL, born_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', national_identifier VARCHAR(255) DEFAULT NULL, dietary_restrictions VARCHAR(1000) DEFAULT NULL, health_restrictions VARCHAR(1000) DEFAULT NULL, medication VARCHAR(1000) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_10FF46133E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_contact (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name_first VARCHAR(255) NOT NULL, name_last VARCHAR(255) NOT NULL, email VARCHAR(180) DEFAULT NULL, phone_number VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', role VARCHAR(32) NOT NULL, role_other VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A7DCE30B3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_form_field_value (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', application_camper_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(32) NOT NULL, label VARCHAR(255) NOT NULL, options LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', is_required TINYINT(1) NOT NULL, help VARCHAR(255) DEFAULT NULL, priority INT NOT NULL, value LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_922E73113E030ACD (application_id), INDEX IDX_922E7311F300DF07 (application_camper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_trip_location_path (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_camper_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', is_there TINYINT(1) NOT NULL, locations LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', location LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B334885F300DF07 (application_camper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1C8047505 FOREIGN KEY (camp_date_id) REFERENCES camp_date (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE application_attachment ADD CONSTRAINT FK_EDCE8573E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_attachment ADD CONSTRAINT FK_EDCE857F300DF07 FOREIGN KEY (application_camper_id) REFERENCES application_camper (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_camper ADD CONSTRAINT FK_10FF46133E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_contact ADD CONSTRAINT FK_A7DCE30B3E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_form_field_value ADD CONSTRAINT FK_922E73113E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_form_field_value ADD CONSTRAINT FK_922E7311F300DF07 FOREIGN KEY (application_camper_id) REFERENCES application_camper (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_trip_location_path ADD CONSTRAINT FK_B334885F300DF07 FOREIGN KEY (application_camper_id) REFERENCES application_camper (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attachment_config ADD help VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE camp_date ADD price_without_deposit DOUBLE PRECISION NOT NULL, CHANGE price deposit DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1C8047505');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395');
        $this->addSql('ALTER TABLE application_attachment DROP FOREIGN KEY FK_EDCE8573E030ACD');
        $this->addSql('ALTER TABLE application_attachment DROP FOREIGN KEY FK_EDCE857F300DF07');
        $this->addSql('ALTER TABLE application_camper DROP FOREIGN KEY FK_10FF46133E030ACD');
        $this->addSql('ALTER TABLE application_contact DROP FOREIGN KEY FK_A7DCE30B3E030ACD');
        $this->addSql('ALTER TABLE application_form_field_value DROP FOREIGN KEY FK_922E73113E030ACD');
        $this->addSql('ALTER TABLE application_form_field_value DROP FOREIGN KEY FK_922E7311F300DF07');
        $this->addSql('ALTER TABLE application_trip_location_path DROP FOREIGN KEY FK_B334885F300DF07');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE application_attachment');
        $this->addSql('DROP TABLE application_camper');
        $this->addSql('DROP TABLE application_contact');
        $this->addSql('DROP TABLE application_form_field_value');
        $this->addSql('DROP TABLE application_trip_location_path');
        $this->addSql('ALTER TABLE attachment_config DROP help');
        $this->addSql('ALTER TABLE camp_date ADD price DOUBLE PRECISION NOT NULL, DROP deposit, DROP price_without_deposit');
    }
}
