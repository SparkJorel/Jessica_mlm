<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623124803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prestation_service (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, name VARCHAR(190) NOT NULL, code VARCHAR(190) NOT NULL, slug VARCHAR(255) NOT NULL, cost INT NOT NULL, pourcentage_prescripteur_snlmembre DOUBLE PRECISION NOT NULL, pourcentage_prescripteur_snl DOUBLE PRECISION NOT NULL, pourcentage_sponsor_prescripteur_snl DOUBLE PRECISION NOT NULL, binaire DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME DEFAULT NULL, ended_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, brochure_service_filename VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_220B694B5E237E06 (name), UNIQUE INDEX UNIQ_220B694B77153098 (code), INDEX IDX_220B694BED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prestation_service ADD CONSTRAINT FK_220B694BED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('DROP TABLE mlm_value_service');
        $this->addSql('ALTER TABLE service ADD slug VARCHAR(255) NOT NULL, CHANGE code code VARCHAR(189) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD277153098 ON service (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mlm_value_service (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, cost INT NOT NULL, binaire DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, pourcentage_prescripteur_snlmembre DOUBLE PRECISION NOT NULL, pourcentage_prescripteur_snl DOUBLE PRECISION NOT NULL, pourcentage_sponsor_prescripteur_snl DOUBLE PRECISION NOT NULL, started_at DATETIME DEFAULT NULL, ended_at DATETIME DEFAULT NULL, brochure_service_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_FC33BA5AED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE mlm_value_service ADD CONSTRAINT FK_FC33BA5AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('DROP TABLE prestation_service');
        $this->addSql('DROP INDEX UNIQ_E19D9AD277153098 ON service');
        $this->addSql('ALTER TABLE service DROP slug, CHANGE code code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
