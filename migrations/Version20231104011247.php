<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231104011247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camp_date_attachment_config (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_date_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', attachment_config_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', priority INT NOT NULL, is_global TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BCCB6E1CC8047505 (camp_date_id), INDEX IDX_BCCB6E1C520354A0 (attachment_config_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camp_date_attachment_config ADD CONSTRAINT FK_BCCB6E1CC8047505 FOREIGN KEY (camp_date_id) REFERENCES camp_date (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camp_date_attachment_config ADD CONSTRAINT FK_BCCB6E1C520354A0 FOREIGN KEY (attachment_config_id) REFERENCES attachment_config (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_attachment_config DROP FOREIGN KEY FK_BCCB6E1CC8047505');
        $this->addSql('ALTER TABLE camp_date_attachment_config DROP FOREIGN KEY FK_BCCB6E1C520354A0');
        $this->addSql('DROP TABLE camp_date_attachment_config');
    }
}
