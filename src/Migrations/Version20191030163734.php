<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191030163734 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tricks ADD type_id_id INT NOT NULL, DROP type');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1714819A0 FOREIGN KEY (type_id_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_E1D902C1714819A0 ON tricks (type_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C1714819A0');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP INDEX IDX_E1D902C1714819A0 ON tricks');
        $this->addSql('ALTER TABLE tricks ADD type VARCHAR(70) NOT NULL COLLATE utf8mb4_unicode_ci, DROP type_id_id');
    }
}
