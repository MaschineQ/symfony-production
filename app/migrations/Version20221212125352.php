<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221212125352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_packaging_material DROP FOREIGN KEY FK_76E2FAF077DB6DC');
        $this->addSql('ALTER TABLE product_packaging_material DROP FOREIGN KEY FK_76E2FAF04584665A');
        $this->addSql('ALTER TABLE product_packaging_material ADD CONSTRAINT FK_76E2FAF077DB6DC FOREIGN KEY (packaging_material_id) REFERENCES packaging_material (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE product_packaging_material ADD CONSTRAINT FK_76E2FAF04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_packaging_material DROP FOREIGN KEY FK_76E2FAF04584665A');
        $this->addSql('ALTER TABLE product_packaging_material DROP FOREIGN KEY FK_76E2FAF077DB6DC');
        $this->addSql('ALTER TABLE product_packaging_material ADD CONSTRAINT FK_76E2FAF04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_packaging_material ADD CONSTRAINT FK_76E2FAF077DB6DC FOREIGN KEY (packaging_material_id) REFERENCES packaging_material (id) ON DELETE CASCADE');
    }
}
