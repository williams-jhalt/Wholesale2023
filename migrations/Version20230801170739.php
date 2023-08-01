<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801170739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD description LONGTEXT DEFAULT NULL, ADD keywords LONGTEXT DEFAULT NULL, ADD price NUMERIC(10, 2) DEFAULT NULL, ADD active TINYINT(1) NOT NULL, ADD barcode VARCHAR(255) DEFAULT NULL, ADD stock_quantity INT NOT NULL, ADD reorder_quantity INT NOT NULL, ADD video TINYINT(1) NOT NULL, ADD on_sale TINYINT(1) NOT NULL, ADD height DOUBLE PRECISION DEFAULT NULL, ADD length DOUBLE PRECISION DEFAULT NULL, ADD width DOUBLE PRECISION DEFAULT NULL, ADD diameter DOUBLE PRECISION DEFAULT NULL, ADD weight DOUBLE PRECISION DEFAULT NULL, ADD color VARCHAR(255) DEFAULT NULL, ADD material VARCHAR(255) DEFAULT NULL, ADD discountable TINYINT(1) NOT NULL, ADD max_discount_rate DOUBLE PRECISION NOT NULL, ADD saleable TINYINT(1) NOT NULL, ADD product_length DOUBLE PRECISION DEFAULT NULL, ADD insertable_length DOUBLE PRECISION DEFAULT NULL, ADD realistic TINYINT(1) NOT NULL, ADD balls TINYINT(1) NOT NULL, ADD suction_cup TINYINT(1) NOT NULL, ADD harness TINYINT(1) NOT NULL, ADD vibrating TINYINT(1) NOT NULL, ADD thick TINYINT(1) NOT NULL, ADD double_ended TINYINT(1) NOT NULL, ADD circumference DOUBLE PRECISION DEFAULT NULL, ADD brand VARCHAR(255) DEFAULT NULL, ADD map_price NUMERIC(10, 2) DEFAULT NULL, ADD amazon_restricted TINYINT(1) NOT NULL, ADD approval_required TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP description, DROP keywords, DROP price, DROP active, DROP barcode, DROP stock_quantity, DROP reorder_quantity, DROP video, DROP on_sale, DROP height, DROP length, DROP width, DROP diameter, DROP weight, DROP color, DROP material, DROP discountable, DROP max_discount_rate, DROP saleable, DROP product_length, DROP insertable_length, DROP realistic, DROP balls, DROP suction_cup, DROP harness, DROP vibrating, DROP thick, DROP double_ended, DROP circumference, DROP brand, DROP map_price, DROP amazon_restricted, DROP approval_required');
    }
}
