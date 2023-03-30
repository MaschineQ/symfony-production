<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201204453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expedition (id INT AUTO_INCREMENT NOT NULL, expedition_date DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expedition_item (id INT AUTO_INCREMENT NOT NULL, expedition_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_11512117576EF81E (expedition_id), INDEX IDX_115121174584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting (id INT NOT NULL, stock_critical INT NOT NULL, stock_low INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE packaging_material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, packaging_type VARCHAR(3) NOT NULL, quantity_per_piece DOUBLE PRECISION NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_packaging_material (product_id INT NOT NULL, packaging_material_id INT NOT NULL, INDEX IDX_76E2FAF04584665A (product_id), INDEX IDX_76E2FAF077DB6DC (packaging_material_id), PRIMARY KEY(product_id, packaging_material_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production (id INT AUTO_INCREMENT NOT NULL, production_date DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production_item (id INT AUTO_INCREMENT NOT NULL, production_id INT NOT NULL, product_id INT NOT NULL, packaging INT NOT NULL, label INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, INDEX IDX_C40B6E0AECC6147F (production_id), INDEX IDX_C40B6E0A4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, receipt_date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt_item (id INT AUTO_INCREMENT NOT NULL, packaging_material_id INT NOT NULL, receipt_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_89601E9277DB6DC (packaging_material_id), INDEX IDX_89601E922B5CA896 (receipt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, locale VARCHAR(5) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D95AB405A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expedition_item ADD CONSTRAINT FK_11512117576EF81E FOREIGN KEY (expedition_id) REFERENCES expedition (id)');
        $this->addSql('ALTER TABLE expedition_item ADD CONSTRAINT FK_115121174584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_packaging_material ADD CONSTRAINT FK_76E2FAF04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_packaging_material ADD CONSTRAINT FK_76E2FAF077DB6DC FOREIGN KEY (packaging_material_id) REFERENCES packaging_material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE production_item ADD CONSTRAINT FK_C40B6E0AECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE production_item ADD CONSTRAINT FK_C40B6E0A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE receipt_item ADD CONSTRAINT FK_89601E9277DB6DC FOREIGN KEY (packaging_material_id) REFERENCES packaging_material (id)');
        $this->addSql('ALTER TABLE receipt_item ADD CONSTRAINT FK_89601E922B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expedition_item DROP FOREIGN KEY FK_11512117576EF81E');
        $this->addSql('ALTER TABLE expedition_item DROP FOREIGN KEY FK_115121174584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_packaging_material DROP FOREIGN KEY FK_76E2FAF04584665A');
        $this->addSql('ALTER TABLE product_packaging_material DROP FOREIGN KEY FK_76E2FAF077DB6DC');
        $this->addSql('ALTER TABLE production_item DROP FOREIGN KEY FK_C40B6E0AECC6147F');
        $this->addSql('ALTER TABLE production_item DROP FOREIGN KEY FK_C40B6E0A4584665A');
        $this->addSql('ALTER TABLE receipt_item DROP FOREIGN KEY FK_89601E9277DB6DC');
        $this->addSql('ALTER TABLE receipt_item DROP FOREIGN KEY FK_89601E922B5CA896');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE expedition');
        $this->addSql('DROP TABLE expedition_item');
        $this->addSql('DROP TABLE notification_setting');
        $this->addSql('DROP TABLE packaging_material');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_packaging_material');
        $this->addSql('DROP TABLE production');
        $this->addSql('DROP TABLE production_item');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE receipt_item');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_profile');
    }
}
