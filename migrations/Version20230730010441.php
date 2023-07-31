<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230730010441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camp (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_category_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, url_name VARCHAR(255) NOT NULL, age_min INT NOT NULL, age_max INT NOT NULL, description_short VARCHAR(160) DEFAULT NULL, description_long VARCHAR(5000) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_C19442304077B7BE (url_name), INDEX IDX_C1944230B10A86AE (camp_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT FK_C1944230B10A86AE FOREIGN KEY (camp_category_id) REFERENCES camp_category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp DROP FOREIGN KEY FK_C1944230B10A86AE');
        $this->addSql('DROP TABLE camp');
    }
}
