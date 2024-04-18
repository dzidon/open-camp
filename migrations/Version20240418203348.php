<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418203348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_payment DROP FOREIGN KEY FK_8696813E2AE7DEAB');
        $this->addSql('DROP TABLE application_payment_state_config');
        $this->addSql('DROP INDEX IDX_8696813E2AE7DEAB ON application_payment');
        $this->addSql('ALTER TABLE application_payment ADD states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD paid_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD cancelled_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD refunded_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD pending_states LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD valid_state_changes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', DROP application_payment_state_config_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application_payment_state_config (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', states LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', paid_states LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', cancelled_states LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', refunded_states LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', pending_states LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', valid_state_changes LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE application_payment ADD application_payment_state_config_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', DROP states, DROP paid_states, DROP cancelled_states, DROP refunded_states, DROP pending_states, DROP valid_state_changes');
        $this->addSql('ALTER TABLE application_payment ADD CONSTRAINT FK_8696813E2AE7DEAB FOREIGN KEY (application_payment_state_config_id) REFERENCES application_payment_state_config (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8696813E2AE7DEAB ON application_payment (application_payment_state_config_id)');
    }
}
