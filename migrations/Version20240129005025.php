<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129005025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON camp_date_user');
        $this->addSql('ALTER TABLE camp_date_user ADD id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD can_update_applications_state TINYINT(1) NOT NULL, ADD can_update_applications TINYINT(1) NOT NULL, ADD can_update_application_payments TINYINT(1) NOT NULL, ADD priority INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON camp_date_user');
        $this->addSql('ALTER TABLE camp_date_user DROP id, DROP can_update_applications_state, DROP can_update_applications, DROP can_update_application_payments, DROP priority, DROP created_at, DROP updated_at');
    }
}
