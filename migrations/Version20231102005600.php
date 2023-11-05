<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231102005600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date ADD trip_location_path_there_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD trip_location_path_back_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE camp_date ADD CONSTRAINT FK_4F7C997263DD4162 FOREIGN KEY (trip_location_path_there_id) REFERENCES trip_location_path (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE camp_date ADD CONSTRAINT FK_4F7C99728B465358 FOREIGN KEY (trip_location_path_back_id) REFERENCES trip_location_path (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4F7C997263DD4162 ON camp_date (trip_location_path_there_id)');
        $this->addSql('CREATE INDEX IDX_4F7C99728B465358 ON camp_date (trip_location_path_back_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date DROP FOREIGN KEY FK_4F7C997263DD4162');
        $this->addSql('ALTER TABLE camp_date DROP FOREIGN KEY FK_4F7C99728B465358');
        $this->addSql('DROP INDEX IDX_4F7C997263DD4162 ON camp_date');
        $this->addSql('DROP INDEX IDX_4F7C99728B465358 ON camp_date');
        $this->addSql('ALTER TABLE camp_date DROP trip_location_path_there_id, DROP trip_location_path_back_id');
    }
}
