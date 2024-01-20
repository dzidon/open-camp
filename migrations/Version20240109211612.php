<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109211612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discount_config (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, recurring_campers_config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', siblings_config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_BACA03435E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP INDEX UNIQ_7B61A1F6772E836A ON payment_method');
        $this->addSql('ALTER TABLE payment_method ADD label VARCHAR(255) NOT NULL, CHANGE identifier name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B61A1F65E237E06 ON payment_method (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE discount_config');
        $this->addSql('DROP INDEX UNIQ_7B61A1F65E237E06 ON payment_method');
        $this->addSql('ALTER TABLE payment_method ADD identifier VARCHAR(255) NOT NULL, DROP name, DROP label');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B61A1F6772E836A ON payment_method (identifier)');
    }
}
