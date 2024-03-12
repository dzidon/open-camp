<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312115657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_user ADD can_manage_applications TINYINT(1) NOT NULL, ADD can_manage_application_payments TINYINT(1) NOT NULL, DROP can_update_applications, DROP can_update_application_payments');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_user ADD can_update_applications TINYINT(1) NOT NULL, ADD can_update_application_payments TINYINT(1) NOT NULL, DROP can_manage_applications, DROP can_manage_application_payments');
    }
}
