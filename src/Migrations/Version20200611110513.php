<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611110513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE produit_magasin (id INT AUTO_INCREMENT NOT NULL, mode_id INT DEFAULT NULL, bien_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, marque VARCHAR(255) DEFAULT NULL, prix INT DEFAULT NULL, nombre_vue INT DEFAULT NULL, media VARCHAR(255) NOT NULL, INDEX IDX_9254D45E77E5854A (mode_id), INDEX IDX_9254D45EBD95B80F (bien_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_magasin ADD CONSTRAINT FK_9254D45E77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE produit_magasin ADD CONSTRAINT FK_9254D45EBD95B80F FOREIGN KEY (bien_id) REFERENCES bien (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE produit_magasin');
    }
}
