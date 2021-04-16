<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200412101554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3AA08CB10 ON utilisateur (login)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D40DE1BFF7747B4 ON topic (titre)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_497DD6346C6E55B5 ON categorie (nom)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52743D7B6C6E55B5 ON sous_categorie (nom)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_497DD6346C6E55B5 ON categorie');
        $this->addSql('DROP INDEX UNIQ_52743D7B6C6E55B5 ON sous_categorie');
        $this->addSql('DROP INDEX UNIQ_9D40DE1BFF7747B4 ON topic');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3AA08CB10 ON utilisateur');
    }
}
