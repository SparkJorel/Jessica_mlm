<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204105059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE grade DROP rewardable');
        $this->addSql('ALTER TABLE promo_bonus_special ADD eligible_grade_id INT DEFAULT NULL, ADD under_condition TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE promo_bonus_special ADD CONSTRAINT FK_3EBB54EB1B447A99 FOREIGN KEY (eligible_grade_id) REFERENCES grade (id)');
        $this->addSql('CREATE INDEX IDX_3EBB54EB1B447A99 ON promo_bonus_special (eligible_grade_id)');
        $this->addSql('ALTER TABLE user DROP token');
        $this->addSql('ALTER TABLE bonus_special ADD weight INT NOT NULL, CHANGE promo_activated promo_activated TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE user_month_carry_over ADD year VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE product DROP recorded_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bonus_special DROP weight, CHANGE promo_activated promo_activated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE grade ADD rewardable TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD recorded_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE promo_bonus_special DROP FOREIGN KEY FK_3EBB54EB1B447A99');
        $this->addSql('DROP INDEX IDX_3EBB54EB1B447A99 ON promo_bonus_special');
        $this->addSql('ALTER TABLE promo_bonus_special DROP eligible_grade_id, DROP under_condition');
        $this->addSql('ALTER TABLE user ADD token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_month_carry_over DROP year');
    }
}
