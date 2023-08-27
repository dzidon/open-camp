<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808015139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camp_image (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', priority INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CB036EC377075ABB (camp_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camp_image ADD CONSTRAINT FK_CB036EC377075ABB FOREIGN KEY (camp_id) REFERENCES camp (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_image DROP FOREIGN KEY FK_CB036EC377075ABB');
        $this->addSql('DROP TABLE camp_image');
    }
}
