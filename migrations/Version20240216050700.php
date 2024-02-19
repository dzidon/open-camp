<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216050700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_purchasable_item_instance ADD application_camper_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE application_purchasable_item_instance ADD CONSTRAINT FK_907069E3F300DF07 FOREIGN KEY (application_camper_id) REFERENCES application_camper (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_907069E3F300DF07 ON application_purchasable_item_instance (application_camper_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_purchasable_item_instance DROP FOREIGN KEY FK_907069E3F300DF07');
        $this->addSql('DROP INDEX IDX_907069E3F300DF07 ON application_purchasable_item_instance');
        $this->addSql('ALTER TABLE application_purchasable_item_instance DROP application_camper_id');
    }
}
