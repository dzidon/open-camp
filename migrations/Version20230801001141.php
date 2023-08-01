<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801001141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD name_last VARCHAR(255) NOT NULL, ADD role VARCHAR(32) NOT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', CHANGE name name_first VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD name VARCHAR(255) NOT NULL, DROP name_first, DROP name_last, DROP role, CHANGE email email VARCHAR(180) NOT NULL, CHANGE phone_number phone_number VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\'');
    }
}
