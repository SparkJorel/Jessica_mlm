<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224055736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, commercial_name VARCHAR(255) NOT NULL, technical_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, maintenance DOUBLE PRECISION DEFAULT NULL, lvl INT DEFAULT NULL, sv DOUBLE PRECISION DEFAULT NULL, rewardable TINYINT(1) DEFAULT \'0\', weight INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo_bonus_special (id INT AUTO_INCREMENT NOT NULL, bonus_special_id INT NOT NULL, eligible_grade_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, status TINYINT(1) NOT NULL, under_condition TINYINT(1) NOT NULL, INDEX IDX_3EBB54EBB0325B08 (bonus_special_id), INDEX IDX_3EBB54EB1B447A99 (eligible_grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bonus_special (id INT AUTO_INCREMENT NOT NULL, grade_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(190) NOT NULL, cap1 INT NOT NULL, cap2 INT NOT NULL, description LONGTEXT DEFAULT NULL, image_file VARCHAR(255) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, started_at DATETIME DEFAULT NULL, status TINYINT(1) NOT NULL, ended_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, promo_activated TINYINT(1) DEFAULT \'0\' NOT NULL, weight INT NOT NULL, UNIQUE INDEX UNIQ_BDF0D7EB989D9B62 (slug), INDEX IDX_BDF0D7EBFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_bg (id INT AUTO_INCREMENT NOT NULL, grade_id INT NOT NULL, lvl_id INT NOT NULL, name VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_84E429D9FE19A1A8 (grade_id), INDEX IDX_84E429D950962F74 (lvl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analyse_fonctionnelle_systematique (id INT AUTO_INCREMENT NOT NULL, group_unit_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, unit TINYINT(1) NOT NULL, INDEX IDX_6A4E54937EBD2401 (group_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level_bonus_generationnel (id INT AUTO_INCREMENT NOT NULL, lvl INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_paid_bonus (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, month VARCHAR(255) NOT NULL, reason VARCHAR(255) NOT NULL, paid_at DATETIME NOT NULL, paid TINYINT(1) NOT NULL, INDEX IDX_ED835FC6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_binary_cycle (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cycle_id INT NOT NULL, binaire DOUBLE PRECISION NOT NULL, side VARCHAR(255) NOT NULL, INDEX IDX_8C26DEDAA76ED395 (user_id), INDEX IDX_8C26DEDA5EC1162 (cycle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_sv (id INT AUTO_INCREMENT NOT NULL, grade_id INT NOT NULL, sv INT NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_BD8D2A3BFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_level (id INT AUTO_INCREMENT NOT NULL, grade_id INT NOT NULL, lvl INT NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_87F3BE3AFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prestation_service_analyse_fonctionnelle_systematique (prestation_service_id INT NOT NULL, analyse_fonctionnelle_systematique_id INT NOT NULL, INDEX IDX_D67713613963AE55 (prestation_service_id), INDEX IDX_D6771361D6A73734 (analyse_fonctionnelle_systematique_id), PRIMARY KEY(prestation_service_id, analyse_fonctionnelle_systematique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_analyse_fonctionnelle_systematique (service_id INT NOT NULL, analyse_fonctionnelle_systematique_id INT NOT NULL, INDEX IDX_ACEA1A08ED5CA9E6 (service_id), INDEX IDX_ACEA1A08D6A73734 (analyse_fonctionnelle_systematique_id), PRIMARY KEY(service_id, analyse_fonctionnelle_systematique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_maintenance (id INT AUTO_INCREMENT NOT NULL, grade_id INT NOT NULL, maintenance DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_B5DA378EFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_grade (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, grade_id INT NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, status TINYINT(1) NOT NULL, INDEX IDX_BB98556CA76ED395 (user_id), INDEX IDX_BB98556CFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_bonus_special (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, bonus_id INT NOT NULL, month VARCHAR(255) DEFAULT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'0\' NOT NULL, promo TINYINT(1) NOT NULL, first_condition TINYINT(1) DEFAULT \'1\' NOT NULL, second_condition TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_537E7ABBA76ED395 (user_id), INDEX IDX_537E7ABB69545666 (bonus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promo_bonus_special ADD CONSTRAINT FK_3EBB54EBB0325B08 FOREIGN KEY (bonus_special_id) REFERENCES bonus_special (id)');
        $this->addSql('ALTER TABLE promo_bonus_special ADD CONSTRAINT FK_3EBB54EB1B447A99 FOREIGN KEY (eligible_grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE bonus_special ADD CONSTRAINT FK_BDF0D7EBFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE grade_bg ADD CONSTRAINT FK_84E429D9FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE grade_bg ADD CONSTRAINT FK_84E429D950962F74 FOREIGN KEY (lvl_id) REFERENCES level_bonus_generationnel (id)');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique ADD CONSTRAINT FK_6A4E54937EBD2401 FOREIGN KEY (group_unit_id) REFERENCES analyse_fonctionnelle_systematique (id)');
        $this->addSql('ALTER TABLE user_paid_bonus ADD CONSTRAINT FK_ED835FC6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_binary_cycle ADD CONSTRAINT FK_8C26DEDAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_binary_cycle ADD CONSTRAINT FK_8C26DEDA5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE grade_sv ADD CONSTRAINT FK_BD8D2A3BFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE grade_level ADD CONSTRAINT FK_87F3BE3AFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_D67713613963AE55 FOREIGN KEY (prestation_service_id) REFERENCES prestation_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_D6771361D6A73734 FOREIGN KEY (analyse_fonctionnelle_systematique_id) REFERENCES analyse_fonctionnelle_systematique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_ACEA1A08ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_ACEA1A08D6A73734 FOREIGN KEY (analyse_fonctionnelle_systematique_id) REFERENCES analyse_fonctionnelle_systematique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grade_maintenance ADD CONSTRAINT FK_B5DA378EFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556CFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE user_bonus_special ADD CONSTRAINT FK_537E7ABBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_bonus_special ADD CONSTRAINT FK_537E7ABB69545666 FOREIGN KEY (bonus_id) REFERENCES bonus_special (id)');
        $this->addSql('ALTER TABLE user ADD parent_id INT DEFAULT NULL, ADD token VARCHAR(255) DEFAULT NULL, ADD is_concerned_by_promo TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8D93D649727ACA70 ON user (parent_id)');
        $this->addSql('ALTER TABLE membership ADD membership_cost DOUBLE PRECISION NOT NULL, ADD membership_groupe_sv DOUBLE PRECISION NOT NULL, ADD membership_product_sv DOUBLE PRECISION NOT NULL, ADD membership_bonus_binaire_pourcent DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE user_month_carry_over ADD year VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE prestation_service ADD slug VARCHAR(255) NOT NULL, ADD duree VARCHAR(20) DEFAULT NULL, CHANGE name name VARCHAR(190) NOT NULL, CHANGE code code VARCHAR(190) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_220B694B5E237E06 ON prestation_service (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_220B694B77153098 ON prestation_service (code)');
        $this->addSql('ALTER TABLE prestation_service RENAME INDEX idx_fc33ba5aed5ca9e6 TO IDX_220B694BED5CA9E6');
        $this->addSql('ALTER TABLE service ADD slug VARCHAR(255) NOT NULL, CHANGE code code VARCHAR(189) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD277153098 ON service (code)');
        $this->addSql('ALTER TABLE product ADD client_price DOUBLE PRECISION NOT NULL, ADD product_cote DOUBLE PRECISION NOT NULL, ADD distributor_price DOUBLE PRECISION NOT NULL, ADD product_sv DOUBLE PRECISION NOT NULL, ADD product_svbpa DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE user_commands ADD code_parrain VARCHAR(255) DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE membership_subscription ADD started_at DATETIME DEFAULT NULL, ADD price DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE promo_bonus_special DROP FOREIGN KEY FK_3EBB54EB1B447A99');
        $this->addSql('ALTER TABLE bonus_special DROP FOREIGN KEY FK_BDF0D7EBFE19A1A8');
        $this->addSql('ALTER TABLE grade_bg DROP FOREIGN KEY FK_84E429D9FE19A1A8');
        $this->addSql('ALTER TABLE grade_sv DROP FOREIGN KEY FK_BD8D2A3BFE19A1A8');
        $this->addSql('ALTER TABLE grade_level DROP FOREIGN KEY FK_87F3BE3AFE19A1A8');
        $this->addSql('ALTER TABLE grade_maintenance DROP FOREIGN KEY FK_B5DA378EFE19A1A8');
        $this->addSql('ALTER TABLE user_grade DROP FOREIGN KEY FK_BB98556CFE19A1A8');
        $this->addSql('ALTER TABLE promo_bonus_special DROP FOREIGN KEY FK_3EBB54EBB0325B08');
        $this->addSql('ALTER TABLE user_bonus_special DROP FOREIGN KEY FK_537E7ABB69545666');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_6A4E54937EBD2401');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_D6771361D6A73734');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_ACEA1A08D6A73734');
        $this->addSql('ALTER TABLE grade_bg DROP FOREIGN KEY FK_84E429D950962F74');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE promo_bonus_special');
        $this->addSql('DROP TABLE bonus_special');
        $this->addSql('DROP TABLE grade_bg');
        $this->addSql('DROP TABLE analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE level_bonus_generationnel');
        $this->addSql('DROP TABLE user_paid_bonus');
        $this->addSql('DROP TABLE user_binary_cycle');
        $this->addSql('DROP TABLE grade_sv');
        $this->addSql('DROP TABLE grade_level');
        $this->addSql('DROP TABLE prestation_service_analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE service_analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE grade_maintenance');
        $this->addSql('DROP TABLE user_grade');
        $this->addSql('DROP TABLE user_bonus_special');
        $this->addSql('ALTER TABLE membership DROP membership_cost, DROP membership_groupe_sv, DROP membership_product_sv, DROP membership_bonus_binaire_pourcent');
        $this->addSql('ALTER TABLE membership_subscription DROP started_at, DROP price');
        $this->addSql('DROP INDEX UNIQ_220B694B5E237E06 ON prestation_service');
        $this->addSql('DROP INDEX UNIQ_220B694B77153098 ON prestation_service');
        $this->addSql('ALTER TABLE prestation_service DROP slug, DROP duree, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE code code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE prestation_service RENAME INDEX idx_220b694bed5ca9e6 TO IDX_FC33BA5AED5CA9E6');
        $this->addSql('ALTER TABLE product DROP client_price, DROP product_cote, DROP distributor_price, DROP product_sv, DROP product_svbpa');
        $this->addSql('DROP INDEX UNIQ_E19D9AD277153098 ON service');
        $this->addSql('ALTER TABLE service DROP slug, CHANGE code code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649727ACA70');
        $this->addSql('DROP INDEX IDX_8D93D649727ACA70 ON user');
        $this->addSql('ALTER TABLE user DROP parent_id, DROP token, DROP is_concerned_by_promo');
        $this->addSql('ALTER TABLE user_commands DROP code_parrain, DROP status, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_month_carry_over DROP year');
    }
}
