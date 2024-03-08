<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305161046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_payment DROP FOREIGN KEY FK_8696813E6C56C3AE');
        $this->addSql('DROP INDEX IDX_8696813E6C56C3AE ON application_payment');
        $this->addSql('ALTER TABLE application_payment CHANGE state_config_id application_payment_state_config_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE application_payment ADD CONSTRAINT FK_8696813E2AE7DEAB FOREIGN KEY (application_payment_state_config_id) REFERENCES application_payment_state_config (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8696813E2AE7DEAB ON application_payment (application_payment_state_config_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_payment DROP FOREIGN KEY FK_8696813E2AE7DEAB');
        $this->addSql('DROP INDEX IDX_8696813E2AE7DEAB ON application_payment');
        $this->addSql('ALTER TABLE application_payment CHANGE application_payment_state_config_id state_config_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE application_payment ADD CONSTRAINT FK_8696813E6C56C3AE FOREIGN KEY (state_config_id) REFERENCES application_payment_state_config (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8696813E6C56C3AE ON application_payment (state_config_id)');
    }
}
