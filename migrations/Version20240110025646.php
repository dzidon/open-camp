<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110025646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD discount_recurring_campers_config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD discount_siblings_config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE camp_date ADD discount_config_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE camp_date ADD CONSTRAINT FK_4F7C997244018E7E FOREIGN KEY (discount_config_id) REFERENCES discount_config (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4F7C997244018E7E ON camp_date (discount_config_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP discount_recurring_campers_config, DROP discount_siblings_config');
        $this->addSql('ALTER TABLE camp_date DROP FOREIGN KEY FK_4F7C997244018E7E');
        $this->addSql('DROP INDEX IDX_4F7C997244018E7E ON camp_date');
        $this->addSql('ALTER TABLE camp_date DROP discount_config_id');
    }
}
