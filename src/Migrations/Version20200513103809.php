<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200513103809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bien (id INT AUTO_INCREMENT NOT NULL, mode_id INT DEFAULT NULL, partenaire_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, prix INT DEFAULT NULL, visite_lien VARCHAR(255) DEFAULT NULL, visite_dossier VARCHAR(255) DEFAULT NULL, nombre_vue INT DEFAULT NULL, debut_promo VARCHAR(255) DEFAULT NULL, fin_promo VARCHAR(255) DEFAULT NULL, google_map VARCHAR(255) DEFAULT NULL, nombre_produit VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, INDEX IDX_45EDC38677E5854A (mode_id), INDEX IDX_45EDC38698DE13AC (partenaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bien_categorie (bien_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_60565B8CBD95B80F (bien_id), INDEX IDX_60565B8CBCF5E72D (categorie_id), PRIMARY KEY(bien_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bien ADD CONSTRAINT FK_45EDC38677E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE bien ADD CONSTRAINT FK_45EDC38698DE13AC FOREIGN KEY (partenaire_id) REFERENCES partenaire (id)');
        $this->addSql('ALTER TABLE bien_categorie ADD CONSTRAINT FK_60565B8CBD95B80F FOREIGN KEY (bien_id) REFERENCES bien (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bien_categorie ADD CONSTRAINT FK_60565B8CBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bien_categorie DROP FOREIGN KEY FK_60565B8CBD95B80F');
        $this->addSql('DROP TABLE bien');
        $this->addSql('DROP TABLE bien_categorie');
    }
}
