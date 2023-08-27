<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230811155145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camp_date (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', camp_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', start_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', price DOUBLE PRECISION NOT NULL, capacity INT NOT NULL, is_closed TINYINT(1) NOT NULL, trip_instructions VARCHAR(2000) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4F7C997277075ABB (camp_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE camp_date_user (camp_date_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_B9E6792DC8047505 (camp_date_id), INDEX IDX_B9E6792DA76ED395 (user_id), PRIMARY KEY(camp_date_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camp_date ADD CONSTRAINT FK_4F7C997277075ABB FOREIGN KEY (camp_id) REFERENCES camp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camp_date_user ADD CONSTRAINT FK_B9E6792DC8047505 FOREIGN KEY (camp_date_id) REFERENCES camp_date (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camp_date_user ADD CONSTRAINT FK_B9E6792DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp_date DROP FOREIGN KEY FK_4F7C997277075ABB');
        $this->addSql('ALTER TABLE camp_date_user DROP FOREIGN KEY FK_B9E6792DC8047505');
        $this->addSql('ALTER TABLE camp_date_user DROP FOREIGN KEY FK_B9E6792DA76ED395');
        $this->addSql('DROP TABLE camp_date');
        $this->addSql('DROP TABLE camp_date_user');
    }
}
