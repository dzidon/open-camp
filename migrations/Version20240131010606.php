<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131010606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS camp_date_user');
        $this->addSql('CREATE TABLE camp_date_user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_date_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', can_update_applications_state TINYINT(1) NOT NULL, can_update_applications TINYINT(1) NOT NULL, can_update_application_payments TINYINT(1) NOT NULL, priority INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B9E6792DC8047505 (camp_date_id), INDEX IDX_B9E6792DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camp_date_user ADD CONSTRAINT FK_B9E6792DC8047505 FOREIGN KEY (camp_date_id) REFERENCES camp_date (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camp_date_user ADD CONSTRAINT FK_B9E6792DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date_user DROP FOREIGN KEY FK_B9E6792DC8047505');
        $this->addSql('ALTER TABLE camp_date_user DROP FOREIGN KEY FK_B9E6792DA76ED395');
        $this->addSql('DROP TABLE camp_date_user');
    }
}
