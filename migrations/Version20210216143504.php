<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216143504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE grade ADD maintenance DOUBLE PRECISION DEFAULT NULL, ADD lvl INT DEFAULT NULL, ADD sv DOUBLE PRECISION DEFAULT NULL, ADD rewardable TINYINT(1) DEFAULT \'0\', ADD weight INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD token VARCHAR(255) DEFAULT NULL, ADD is_concerned_by_promo TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE membership ADD membership_cost DOUBLE PRECISION NOT NULL, ADD membership_groupe_sv DOUBLE PRECISION NOT NULL, ADD membership_product_sv DOUBLE PRECISION NOT NULL, ADD membership_bonus_binaire_pourcent DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE product ADD client_price DOUBLE PRECISION NOT NULL, ADD product_cote DOUBLE PRECISION NOT NULL, ADD distributor_price DOUBLE PRECISION NOT NULL, ADD product_sv DOUBLE PRECISION NOT NULL, ADD product_svbpa DOUBLE PRECISION NOT NULL, ADD recorded_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE grade DROP maintenance, DROP lvl, DROP sv, DROP rewardable, DROP weight');
        $this->addSql('ALTER TABLE membership DROP membership_cost, DROP membership_groupe_sv, DROP membership_product_sv, DROP membership_bonus_binaire_pourcent');
        $this->addSql('ALTER TABLE product DROP client_price, DROP product_cote, DROP distributor_price, DROP product_sv, DROP product_svbpa, DROP recorded_at');
        $this->addSql('ALTER TABLE user DROP token, DROP is_concerned_by_promo');
    }
}
