<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103004355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camp_date_form_field (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_date_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', form_field_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', priority INT NOT NULL, is_global TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C623B972C8047505 (camp_date_id), INDEX IDX_C623B972F50D82F4 (form_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camp_date_form_field ADD CONSTRAINT FK_C623B972C8047505 FOREIGN KEY (camp_date_id) REFERENCES camp_date (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camp_date_form_field ADD CONSTRAINT FK_C623B972F50D82F4 FOREIGN KEY (form_field_id) REFERENCES form_field (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_form_field DROP FOREIGN KEY FK_C623B972C8047505');
        $this->addSql('ALTER TABLE camp_date_form_field DROP FOREIGN KEY FK_C623B972F50D82F4');
        $this->addSql('DROP TABLE camp_date_form_field');
    }
}
