<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611160555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE produit_magasin_famille (produit_magasin_id INT NOT NULL, famille_id INT NOT NULL, INDEX IDX_73C2EB5D893153EE (produit_magasin_id), INDEX IDX_73C2EB5D97A77B84 (famille_id), PRIMARY KEY(produit_magasin_id, famille_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_magasin_famille ADD CONSTRAINT FK_73C2EB5D893153EE FOREIGN KEY (produit_magasin_id) REFERENCES produit_magasin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_magasin_famille ADD CONSTRAINT FK_73C2EB5D97A77B84 FOREIGN KEY (famille_id) REFERENCES famille (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE produit_magasin_famille');
    }
}
