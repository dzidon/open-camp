<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231025233811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camper_camper DROP FOREIGN KEY FK_5EA1AEFFA1E79B5A');
        $this->addSql('ALTER TABLE camper_camper DROP FOREIGN KEY FK_5EA1AEFFB802CBD5');
        $this->addSql('DROP TABLE camper_camper');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camper_camper (camper_source BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camper_target BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_5EA1AEFFA1E79B5A (camper_source), INDEX IDX_5EA1AEFFB802CBD5 (camper_target), PRIMARY KEY(camper_source, camper_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE camper_camper ADD CONSTRAINT FK_5EA1AEFFA1E79B5A FOREIGN KEY (camper_source) REFERENCES camper (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camper_camper ADD CONSTRAINT FK_5EA1AEFFB802CBD5 FOREIGN KEY (camper_target) REFERENCES camper (id) ON DELETE CASCADE');
    }
}
