<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211007115658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE promo_bonus_special (id INT AUTO_INCREMENT NOT NULL, bonus_special_id INT NOT NULL, eligible_grade_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, status TINYINT(1) NOT NULL, under_condition TINYINT(1) NOT NULL, INDEX IDX_3EBB54EBB0325B08 (bonus_special_id), INDEX IDX_3EBB54EB1B447A99 (eligible_grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, command_id INT DEFAULT NULL, seen TINYINT(1) DEFAULT \'0\', discr VARCHAR(255) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA33E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bonus_special (id INT AUTO_INCREMENT NOT NULL, grade_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(190) NOT NULL, cap1 INT NOT NULL, cap2 INT NOT NULL, description LONGTEXT DEFAULT NULL, image_file VARCHAR(255) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, started_at DATETIME DEFAULT NULL, status TINYINT(1) NOT NULL, ended_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, promo_activated TINYINT(1) DEFAULT \'0\' NOT NULL, weight INT NOT NULL, UNIQUE INDEX UNIQ_BDF0D7EB989D9B62 (slug), INDEX IDX_BDF0D7EBFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commission_indirect_bonus (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lvl INT NOT NULL, reason VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, month VARCHAR(20) NOT NULL, year VARCHAR(50) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, INDEX IDX_1B298D9DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analyse_fonctionnelle_systematique (id INT AUTO_INCREMENT NOT NULL, group_unit_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, unit TINYINT(1) NOT NULL, INDEX IDX_6A4E54937EBD2401 (group_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_binary_cycle (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cycle_id INT NOT NULL, binaire DOUBLE PRECISION NOT NULL, side VARCHAR(255) NOT NULL, INDEX IDX_8C26DEDAA76ED395 (user_id), INDEX IDX_8C26DEDA5EC1162 (cycle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prestation_service (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, name VARCHAR(190) NOT NULL, code VARCHAR(190) NOT NULL, slug VARCHAR(255) NOT NULL, cost INT NOT NULL, pourcentage_prescripteur_snlmembre DOUBLE PRECISION NOT NULL, pourcentage_prescripteur_snl DOUBLE PRECISION NOT NULL, pourcentage_sponsor_prescripteur_snl DOUBLE PRECISION NOT NULL, binaire DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME DEFAULT NULL, ended_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, brochure_service_filename VARCHAR(255) DEFAULT NULL, duree VARCHAR(20) DEFAULT NULL, UNIQUE INDEX UNIQ_220B694B5E237E06 (name), UNIQUE INDEX UNIQ_220B694B77153098 (code), INDEX IDX_220B694BED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prestation_service_analyse_fonctionnelle_systematique (prestation_service_id INT NOT NULL, analyse_fonctionnelle_systematique_id INT NOT NULL, INDEX IDX_D67713613963AE55 (prestation_service_id), INDEX IDX_D6771361D6A73734 (analyse_fonctionnelle_systematique_id), PRIMARY KEY(prestation_service_id, analyse_fonctionnelle_systematique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(189) NOT NULL, slug VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, recorded_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E19D9AD277153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_analyse_fonctionnelle_systematique (service_id INT NOT NULL, analyse_fonctionnelle_systematique_id INT NOT NULL, INDEX IDX_ACEA1A08ED5CA9E6 (service_id), INDEX IDX_ACEA1A08D6A73734 (analyse_fonctionnelle_systematique_id), PRIMARY KEY(service_id, analyse_fonctionnelle_systematique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indirect_bonus_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, lvl INT NOT NULL, INDEX IDX_AB372DA74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indirect_bonus_membership (id INT AUTO_INCREMENT NOT NULL, membership_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, lvl INT NOT NULL, INDEX IDX_E19AC5411FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE summary_commission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, reason VARCHAR(255) NOT NULL, amount DOUBLE PRECISION DEFAULT NULL, started_at DATETIME NOT NULL, month VARCHAR(20) NOT NULL, year VARCHAR(50) NOT NULL, ended_at DATETIME NOT NULL, status TINYINT(1) DEFAULT \'0\', INDEX IDX_3CE40090A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_bonus_special (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, bonus_id INT NOT NULL, month VARCHAR(255) DEFAULT NULL, recorded_at DATETIME NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'0\' NOT NULL, promo TINYINT(1) NOT NULL, first_condition TINYINT(1) DEFAULT \'1\' NOT NULL, second_condition TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_537E7ABBA76ED395 (user_id), INDEX IDX_537E7ABB69545666 (bonus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promo_bonus_special ADD CONSTRAINT FK_3EBB54EBB0325B08 FOREIGN KEY (bonus_special_id) REFERENCES bonus_special (id)');
        $this->addSql('ALTER TABLE promo_bonus_special ADD CONSTRAINT FK_3EBB54EB1B447A99 FOREIGN KEY (eligible_grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA33E1689A FOREIGN KEY (command_id) REFERENCES user_commands (id)');
        $this->addSql('ALTER TABLE bonus_special ADD CONSTRAINT FK_BDF0D7EBFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE commission_indirect_bonus ADD CONSTRAINT FK_1B298D9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique ADD CONSTRAINT FK_6A4E54937EBD2401 FOREIGN KEY (group_unit_id) REFERENCES analyse_fonctionnelle_systematique (id)');
        $this->addSql('ALTER TABLE user_binary_cycle ADD CONSTRAINT FK_8C26DEDAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_binary_cycle ADD CONSTRAINT FK_8C26DEDA5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE prestation_service ADD CONSTRAINT FK_220B694BED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_D67713613963AE55 FOREIGN KEY (prestation_service_id) REFERENCES prestation_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_D6771361D6A73734 FOREIGN KEY (analyse_fonctionnelle_systematique_id) REFERENCES analyse_fonctionnelle_systematique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_ACEA1A08ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique ADD CONSTRAINT FK_ACEA1A08D6A73734 FOREIGN KEY (analyse_fonctionnelle_systematique_id) REFERENCES analyse_fonctionnelle_systematique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE indirect_bonus_product ADD CONSTRAINT FK_AB372DA74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE indirect_bonus_membership ADD CONSTRAINT FK_E19AC5411FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE summary_commission ADD CONSTRAINT FK_3CE40090A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_bonus_special ADD CONSTRAINT FK_537E7ABBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_bonus_special ADD CONSTRAINT FK_537E7ABB69545666 FOREIGN KEY (bonus_id) REFERENCES bonus_special (id)');
        $this->addSql('ALTER TABLE grade ADD maintenance DOUBLE PRECISION DEFAULT NULL, ADD lvl INT DEFAULT NULL, ADD sv DOUBLE PRECISION DEFAULT NULL, ADD rewardable TINYINT(1) DEFAULT \'0\', ADD weight INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD user_grade_id INT DEFAULT NULL, ADD code_distributor VARCHAR(255) DEFAULT NULL, ADD token VARCHAR(255) DEFAULT NULL, ADD is_concerned_by_promo TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491E9445A8 FOREIGN KEY (user_grade_id) REFERENCES grade (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6491E9445A8 ON user (user_grade_id)');
        $this->addSql('ALTER TABLE membership ADD membership_cost DOUBLE PRECISION NOT NULL, ADD membership_groupe_sv DOUBLE PRECISION NOT NULL, ADD membership_product_cote DOUBLE PRECISION NOT NULL, ADD membership_bonus_binaire_pourcent DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE user_paid_bonus ADD year VARCHAR(255) DEFAULT NULL, ADD started_at DATETIME DEFAULT NULL, ADD ended_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_month_carry_over ADD binaire DOUBLE PRECISION DEFAULT NULL, ADD sv_gain DOUBLE PRECISION DEFAULT NULL, ADD gain DOUBLE PRECISION DEFAULT NULL, ADD left_side_sponsoring_sv DOUBLE PRECISION DEFAULT NULL, ADD left_side_achat_sv DOUBLE PRECISION DEFAULT NULL, ADD left_side_total_sv DOUBLE PRECISION DEFAULT NULL, ADD right_side_sponsoring_sv DOUBLE PRECISION DEFAULT NULL, ADD right_side_achat_sv DOUBLE PRECISION DEFAULT NULL, ADD right_side_total_sv DOUBLE PRECISION DEFAULT NULL, ADD old_co DOUBLE PRECISION DEFAULT NULL, ADD left_or_right_side_new_total_sv DOUBLE PRECISION DEFAULT NULL, ADD old_position VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE command_products ADD item_distributor_price DOUBLE PRECISION DEFAULT NULL, ADD item_client_price DOUBLE PRECISION DEFAULT NULL, ADD item_svap DOUBLE PRECISION DEFAULT NULL, ADD item_svbinaire DOUBLE PRECISION DEFAULT NULL, ADD distributor TINYINT(1) DEFAULT \'1\'');
        $this->addSql('ALTER TABLE cycle ADD auto_save TINYINT(1) DEFAULT \'0\', ADD binary_saved TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE product ADD client_price DOUBLE PRECISION NOT NULL, ADD product_cote DOUBLE PRECISION NOT NULL, ADD distributor_price DOUBLE PRECISION NOT NULL, ADD product_sv DOUBLE PRECISION NOT NULL, ADD product_svbpa DOUBLE PRECISION NOT NULL, ADD recorded_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_commands ADD code_parrain VARCHAR(255) DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL, ADD distributor TINYINT(1) DEFAULT \'1\', ADD total_distributor_price DOUBLE PRECISION DEFAULT NULL, ADD total_client_price DOUBLE PRECISION DEFAULT NULL, ADD total_svap DOUBLE PRECISION DEFAULT NULL, ADD total_svbinaire DOUBLE PRECISION DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE membership_subscription ADD total_svbinaire DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE promo_bonus_special DROP FOREIGN KEY FK_3EBB54EBB0325B08');
        $this->addSql('ALTER TABLE user_bonus_special DROP FOREIGN KEY FK_537E7ABB69545666');
        $this->addSql('ALTER TABLE analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_6A4E54937EBD2401');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_D6771361D6A73734');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_ACEA1A08D6A73734');
        $this->addSql('ALTER TABLE prestation_service_analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_D67713613963AE55');
        $this->addSql('ALTER TABLE prestation_service DROP FOREIGN KEY FK_220B694BED5CA9E6');
        $this->addSql('ALTER TABLE service_analyse_fonctionnelle_systematique DROP FOREIGN KEY FK_ACEA1A08ED5CA9E6');
        $this->addSql('DROP TABLE promo_bonus_special');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE bonus_special');
        $this->addSql('DROP TABLE commission_indirect_bonus');
        $this->addSql('DROP TABLE analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE user_binary_cycle');
        $this->addSql('DROP TABLE prestation_service');
        $this->addSql('DROP TABLE prestation_service_analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_analyse_fonctionnelle_systematique');
        $this->addSql('DROP TABLE indirect_bonus_product');
        $this->addSql('DROP TABLE indirect_bonus_membership');
        $this->addSql('DROP TABLE summary_commission');
        $this->addSql('DROP TABLE user_bonus_special');
        $this->addSql('ALTER TABLE command_products DROP item_distributor_price, DROP item_client_price, DROP item_svap, DROP item_svbinaire, DROP distributor');
        $this->addSql('ALTER TABLE cycle DROP auto_save, DROP binary_saved');
        $this->addSql('ALTER TABLE grade DROP maintenance, DROP lvl, DROP sv, DROP rewardable, DROP weight');
        $this->addSql('ALTER TABLE membership DROP membership_cost, DROP membership_groupe_sv, DROP membership_product_cote, DROP membership_bonus_binaire_pourcent');
        $this->addSql('ALTER TABLE membership_subscription DROP total_svbinaire');
        $this->addSql('ALTER TABLE product DROP client_price, DROP product_cote, DROP distributor_price, DROP product_sv, DROP product_svbpa, DROP recorded_at');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491E9445A8');
        $this->addSql('DROP INDEX IDX_8D93D6491E9445A8 ON user');
        $this->addSql('ALTER TABLE user DROP user_grade_id, DROP code_distributor, DROP token, DROP is_concerned_by_promo');
        $this->addSql('ALTER TABLE user_commands DROP code_parrain, DROP status, DROP distributor, DROP total_distributor_price, DROP total_client_price, DROP total_svap, DROP total_svbinaire, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_month_carry_over DROP binaire, DROP sv_gain, DROP gain, DROP left_side_sponsoring_sv, DROP left_side_achat_sv, DROP left_side_total_sv, DROP right_side_sponsoring_sv, DROP right_side_achat_sv, DROP right_side_total_sv, DROP old_co, DROP left_or_right_side_new_total_sv, DROP old_position');
        $this->addSql('ALTER TABLE user_paid_bonus DROP year, DROP started_at, DROP ended_at');
    }
}
