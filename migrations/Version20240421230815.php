<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421230815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallery_image (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', gallery_image_category_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', extension VARCHAR(8) NOT NULL, is_hidden_in_gallery TINYINT(1) NOT NULL, is_in_carousel TINYINT(1) NOT NULL, carousel_priority INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_21A0D47CEEEFE461 (gallery_image_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gallery_image ADD CONSTRAINT FK_21A0D47CEEEFE461 FOREIGN KEY (gallery_image_category_id) REFERENCES gallery_image_category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery_image DROP FOREIGN KEY FK_21A0D47CEEEFE461');
        $this->addSql('DROP TABLE gallery_image');
    }
}
