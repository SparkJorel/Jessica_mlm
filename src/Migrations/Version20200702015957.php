<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702015957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE promo_bonus_special (id INT AUTO_INCREMENT NOT NULL, bonus_special_id INT NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_3EBB54EBB0325B08 (bonus_special_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promo_bonus_special ADD CONSTRAINT FK_3EBB54EBB0325B08 FOREIGN KEY (bonus_special_id) REFERENCES bonus_special (id)');
        $this->addSql('ALTER TABLE bonus_special ADD promo_activated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE prestation_service ADD duree VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE promo_bonus_special');
        $this->addSql('ALTER TABLE bonus_special DROP promo_activated');
        $this->addSql('ALTER TABLE prestation_service DROP duree');
    }
}
