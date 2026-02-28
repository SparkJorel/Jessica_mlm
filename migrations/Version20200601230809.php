<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601230809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestation_service ADD service_id INT NOT NULL, ADD brochure_service_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE prestation_service ADD CONSTRAINT FK_FC33BA5AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_FC33BA5AED5CA9E6 ON prestation_service (service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestation_service DROP FOREIGN KEY FK_FC33BA5AED5CA9E6');
        $this->addSql('DROP INDEX IDX_FC33BA5AED5CA9E6 ON prestation_service');
        $this->addSql('ALTER TABLE prestation_service DROP service_id, DROP brochure_service_filename');
    }
}
