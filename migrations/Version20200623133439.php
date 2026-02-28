<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623133439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prestation_service_analyse_fonctionnelle_systematique (prestation_service_id INT NOT NULL, analyse_fonctionnelle_systematique_id INT NOT NULL, INDEX IDX_D67713613963AE55 (prestation_service_id), INDEX IDX_D6771361D6A73734 (analyse_fonctionnelle_systematique_id), PRIMARY KEY(prestation_service_id, analyse_fonctionnelle_systematique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_analyse_fonctionnelle_systematique (service_id INT NOT NULL, analyse_fonctionnelle_systematique_id INT NOT NULL, INDEX IDX_ACEA1A08ED5CA9E6 (service_id), INDEX IDX_ACEA1A08D6A73734 (analyse_fonctionnelle_systematique_id), PRIMARY KEY(service_id, analyse_fonctionnelle_systematique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_D67713613963AE55 FOREIGN KEY (prestation_service_id) REFERENCES prestation_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_D6771361D6A73734 FOREIGN KEY (analyse_fonctionnelle_systematique_id) REFERENCES analyse_fonctionnelle_systematique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_ACEA1A08ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_ACEA1A08D6A73734 FOREIGN KEY (analyse_fonctionnelle_systematique_id) REFERENCES analyse_fonctionnelle_systematique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique ADD group_unit_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD unit TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique ADD CONSTRAINT FK_6A4E54937EBD2401 FOREIGN KEY (group_unit_id) REFERENCES analyse_fonctionnelle_systematique (id)');
        $this->addSql('CREATE INDEX IDX_6A4E54937EBD2401 ON analyse_fonctionnelle_systematique (group_unit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE prestation_service_analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE service_analyse_fonctionnelle_systematique');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_6A4E54937EBD2401');
        $this->addSql('DROP INDEX IDX_6A4E54937EBD2401 ON analyse_fonctionnelle_systematique');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique DROP group_unit_id, DROP name, DROP description, DROP unit');
    }
}
