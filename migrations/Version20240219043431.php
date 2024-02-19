<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219043431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_camper ADD priority INT NOT NULL');
        $this->addSql('ALTER TABLE application_contact ADD priority INT NOT NULL');
        $this->addSql('ALTER TABLE application_purchasable_item_instance ADD priority INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_camper DROP priority');
        $this->addSql('ALTER TABLE application_contact DROP priority');
        $this->addSql('ALTER TABLE application_purchasable_item_instance DROP priority');
    }
}
