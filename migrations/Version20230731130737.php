<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731130737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, customer_number VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, company VARCHAR(255) NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, address3 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weborder (id INT AUTO_INCREMENT NOT NULL, order_number VARCHAR(255) NOT NULL, reference1 VARCHAR(255) DEFAULT NULL, reference2 VARCHAR(255) DEFAULT NULL, reference3 VARCHAR(255) DEFAULT NULL, ship_to_name VARCHAR(255) NOT NULL, ship_to_address VARCHAR(255) NOT NULL, ship_to_address2 VARCHAR(255) DEFAULT NULL, ship_to_address3 VARCHAR(255) DEFAULT NULL, ship_to_city VARCHAR(255) NOT NULL, ship_to_state VARCHAR(255) DEFAULT NULL, ship_to_zip VARCHAR(255) DEFAULT NULL, ship_to_country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weborder_item (id INT AUTO_INCREMENT NOT NULL, weborder_id INT NOT NULL, item_number VARCHAR(255) NOT NULL, quantity INT NOT NULL, INDEX IDX_C1DA7FC0117822DB (weborder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE weborder_item ADD CONSTRAINT FK_C1DA7FC0117822DB FOREIGN KEY (weborder_id) REFERENCES weborder (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE weborder_item DROP FOREIGN KEY FK_C1DA7FC0117822DB');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE weborder');
        $this->addSql('DROP TABLE weborder_item');
    }
}
