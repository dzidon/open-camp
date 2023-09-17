<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230913023248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attachment_config (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, max_size INT NOT NULL, is_required TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_968E294E5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attachment_config_file_extension (attachment_config_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', file_extension_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_50153545520354A0 (attachment_config_id), INDEX IDX_50153545AB8C6E61 (file_extension_id), PRIMARY KEY(attachment_config_id, file_extension_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_extension (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', extension VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_11B882019FB73D77 (extension), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attachment_config_file_extension ADD CONSTRAINT FK_50153545520354A0 FOREIGN KEY (attachment_config_id) REFERENCES attachment_config (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attachment_config_file_extension ADD CONSTRAINT FK_50153545AB8C6E61 FOREIGN KEY (file_extension_id) REFERENCES file_extension (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment_config_file_extension DROP FOREIGN KEY FK_50153545520354A0');
        $this->addSql('ALTER TABLE attachment_config_file_extension DROP FOREIGN KEY FK_50153545AB8C6E61');
        $this->addSql('DROP TABLE attachment_config');
        $this->addSql('DROP TABLE attachment_config_file_extension');
        $this->addSql('DROP TABLE file_extension');
    }
}
