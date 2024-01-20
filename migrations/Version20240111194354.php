<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240111194354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD discount_siblings_interval_from INT NOT NULL, ADD discount_siblings_interval_to INT NOT NULL');
        $this->addSql('ALTER TABLE application_camper ADD trips_in_the_past INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP discount_siblings_interval_from, DROP discount_siblings_interval_to');
        $this->addSql('ALTER TABLE application_camper DROP trips_in_the_past');
    }
}
