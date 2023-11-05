<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231104183123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_attachment_config DROP is_global');
        $this->addSql('ALTER TABLE camp_date_form_field DROP is_global');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_attachment_config ADD is_global TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE camp_date_form_field ADD is_global TINYINT(1) NOT NULL');
    }
}
