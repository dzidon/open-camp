<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127190103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD born_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', ADD bio VARCHAR(2000) DEFAULT NULL, ADD image_extension VARCHAR(8) DEFAULT NULL, ADD url_name VARCHAR(255) DEFAULT NULL, DROP leader_phone_number');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD leader_phone_number VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', DROP born_at, DROP bio, DROP image_extension, DROP url_name');
    }
}
