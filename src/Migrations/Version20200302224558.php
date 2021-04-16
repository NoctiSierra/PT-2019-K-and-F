<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302224558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, type_utilisateur_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, adresse LONGTEXT DEFAULT NULL, login VARCHAR(255) NOT NULL, numero VARCHAR(10) DEFAULT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3AD4BC8DB (type_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, titre LONGTEXT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, id_sous_categorie_id INT DEFAULT NULL, id_utilisateur_id INT NOT NULL, titre LONGTEXT NOT NULL, contenu LONGTEXT NOT NULL, date_heure_creation DATETIME NOT NULL, INDEX IDX_9D40DE1BF252D75F (id_sous_categorie_id), INDEX IDX_9D40DE1BC6EE5C49 (id_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_fiche (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_categorie (id INT AUTO_INCREMENT NOT NULL, id_categorie_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_52743D7B9F34925F (id_categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiche (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, type_fiche_id INT NOT NULL, title LONGTEXT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4C13CC78F675F31B (author_id), INDEX IDX_4C13CC7812AD02F4 (type_fiche_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, id_posteur_id INT NOT NULL, id_destinataire_id INT NOT NULL, id_topic_id INT NOT NULL, date_envoi DATETIME NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_B6BD307F3856C464 (id_posteur_id), INDEX IDX_B6BD307F4DA68CE6 (id_destinataire_id), INDEX IDX_B6BD307F4F8ECCA8 (id_topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3AD4BC8DB FOREIGN KEY (type_utilisateur_id) REFERENCES type_utilisateur (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BF252D75F FOREIGN KEY (id_sous_categorie_id) REFERENCES sous_categorie (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE sous_categorie ADD CONSTRAINT FK_52743D7B9F34925F FOREIGN KEY (id_categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78F675F31B FOREIGN KEY (author_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC7812AD02F4 FOREIGN KEY (type_fiche_id) REFERENCES type_fiche (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3856C464 FOREIGN KEY (id_posteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F4DA68CE6 FOREIGN KEY (id_destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F4F8ECCA8 FOREIGN KEY (id_topic_id) REFERENCES topic (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BC6EE5C49');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78F675F31B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F3856C464');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F4DA68CE6');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F4F8ECCA8');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC7812AD02F4');
        $this->addSql('ALTER TABLE sous_categorie DROP FOREIGN KEY FK_52743D7B9F34925F');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BF252D75F');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3AD4BC8DB');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE type_fiche');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE sous_categorie');
        $this->addSql('DROP TABLE fiche');
        $this->addSql('DROP TABLE type_utilisateur');
        $this->addSql('DROP TABLE message');
    }
}
