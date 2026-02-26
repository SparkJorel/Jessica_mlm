<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601230528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE service DROP image_name');
        $this->addSql('ALTER TABLE prestation_service DROP FOREIGN KEY FK_FC33BA5AED5CA9E6');
        $this->addSql('DROP INDEX IDX_FC33BA5AED5CA9E6 ON prestation_service');
        $this->addSql('ALTER TABLE prestation_service ADD name VARCHAR(255) NOT NULL, ADD code VARCHAR(255) NOT NULL, ADD pourcentage_prescripteur_snlmembre DOUBLE PRECISION NOT NULL, ADD pourcentage_prescripteur_snl DOUBLE PRECISION NOT NULL, ADD pourcentage_sponsor_prescripteur_snl DOUBLE PRECISION NOT NULL, ADD started_at DATETIME DEFAULT NULL, ADD ended_at DATETIME DEFAULT NULL, DROP service_id, DROP prescripteur_membre, DROP prescripteur_snl, DROP sponsor');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestation_service ADD service_id INT NOT NULL, ADD prescripteur_membre DOUBLE PRECISION NOT NULL, ADD prescripteur_snl DOUBLE PRECISION NOT NULL, ADD sponsor DOUBLE PRECISION NOT NULL, DROP name, DROP code, DROP pourcentage_prescripteur_snlmembre, DROP pourcentage_prescripteur_snl, DROP pourcentage_sponsor_prescripteur_snl, DROP started_at, DROP ended_at');
        $this->addSql('ALTER TABLE prestation_service ADD CONSTRAINT FK_FC33BA5AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_FC33BA5AED5CA9E6 ON prestation_service (service_id)');
        $this->addSql('ALTER TABLE service ADD image_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
