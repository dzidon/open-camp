<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231230225839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application_purchasable_item (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', purchasable_item_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', application_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, max_amount INT NOT NULL, description VARCHAR(2000) DEFAULT NULL, valid_variant_values LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6343508D16B4E87 (purchasable_item_id), INDEX IDX_63435083E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_purchasable_item_instance (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', application_purchasable_item_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', chosen_variant_values LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', amount INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_907069E3EBD7A28D (application_purchasable_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application_purchasable_item ADD CONSTRAINT FK_6343508D16B4E87 FOREIGN KEY (purchasable_item_id) REFERENCES purchasable_item (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE application_purchasable_item ADD CONSTRAINT FK_63435083E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_purchasable_item_instance ADD CONSTRAINT FK_907069E3EBD7A28D FOREIGN KEY (application_purchasable_item_id) REFERENCES application_purchasable_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_purchasable_item DROP FOREIGN KEY FK_6343508D16B4E87');
        $this->addSql('ALTER TABLE application_purchasable_item DROP FOREIGN KEY FK_63435083E030ACD');
        $this->addSql('ALTER TABLE application_purchasable_item_instance DROP FOREIGN KEY FK_907069E3EBD7A28D');
        $this->addSql('DROP TABLE application_purchasable_item');
        $this->addSql('DROP TABLE application_purchasable_item_instance');
    }
}
