<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221202623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAFE54D947');
        $this->addSql('DROP INDEX IDX_E04992AAFE54D947 ON permission');
        $this->addSql('ALTER TABLE permission CHANGE group_id permission_group_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAB6C0CF1 FOREIGN KEY (permission_group_id) REFERENCES permission_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E04992AAB6C0CF1 ON permission (permission_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAB6C0CF1');
        $this->addSql('DROP INDEX IDX_E04992AAB6C0CF1 ON permission');
        $this->addSql('ALTER TABLE permission CHANGE permission_group_id group_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAFE54D947 FOREIGN KEY (group_id) REFERENCES permission_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E04992AAFE54D947 ON permission (group_id)');
    }
}
