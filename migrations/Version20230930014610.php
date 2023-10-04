<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930014610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchasable_item (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, max_amount_per_camper INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_55A118C75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchasable_item_variant (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', purchasable_item_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, priority INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_83EA40A75E237E06 (name), INDEX IDX_83EA40A7D16B4E87 (purchasable_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchasable_item_variant_value (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', purchasable_item_variant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, priority INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_9114C9325E237E06 (name), INDEX IDX_9114C932AD0CA35 (purchasable_item_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchasable_item_variant ADD CONSTRAINT FK_83EA40A7D16B4E87 FOREIGN KEY (purchasable_item_id) REFERENCES purchasable_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchasable_item_variant_value ADD CONSTRAINT FK_9114C932AD0CA35 FOREIGN KEY (purchasable_item_variant_id) REFERENCES purchasable_item_variant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchasable_item_variant DROP FOREIGN KEY FK_83EA40A7D16B4E87');
        $this->addSql('ALTER TABLE purchasable_item_variant_value DROP FOREIGN KEY FK_9114C932AD0CA35');
        $this->addSql('DROP TABLE purchasable_item');
        $this->addSql('DROP TABLE purchasable_item_variant');
        $this->addSql('DROP TABLE purchasable_item_variant_value');
    }
}
