<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305160450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application_payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', state_config_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', amount DOUBLE PRECISION NOT NULL, type VARCHAR(16) NOT NULL, state VARCHAR(255) NOT NULL, is_online TINYINT(1) NOT NULL, external_id VARCHAR(255) DEFAULT NULL, external_url VARCHAR(1000) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8696813E9F75D7B0 (external_id), UNIQUE INDEX UNIQ_8696813E545CEB66 (external_url), INDEX IDX_8696813E6C56C3AE (state_config_id), INDEX IDX_8696813E3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_payment_state_config (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', paid_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', cancelled_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', refunded_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', pending_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', valid_state_changes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application_payment ADD CONSTRAINT FK_8696813E6C56C3AE FOREIGN KEY (state_config_id) REFERENCES application_payment_state_config (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_payment ADD CONSTRAINT FK_8696813E3E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_payment DROP FOREIGN KEY FK_8696813E6C56C3AE');
        $this->addSql('ALTER TABLE application_payment DROP FOREIGN KEY FK_8696813E3E030ACD');
        $this->addSql('DROP TABLE application_payment');
        $this->addSql('DROP TABLE application_payment_state_config');
    }
}
