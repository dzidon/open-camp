<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410205251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_post (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', author_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(32) NOT NULL, url_name VARCHAR(255) NOT NULL, description VARCHAR(160) DEFAULT NULL, content VARCHAR(5000) NOT NULL, is_hidden TINYINT(1) NOT NULL, is_pinned TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_BA5AE01D4077B7BE (url_name), INDEX IDX_BA5AE01DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_view (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', blog_post_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', visitor_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_92D624CAA77FBEAF (blog_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blog_post_view ADD CONSTRAINT FK_92D624CAA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DF675F31B');
        $this->addSql('ALTER TABLE blog_post_view DROP FOREIGN KEY FK_92D624CAA77FBEAF');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_post_view');
    }
}
