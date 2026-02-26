<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190927092657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE membership_bonus_pourcentage (id INT AUTO_INCREMENT NOT NULL, membership_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, state TINYINT(1) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_6694E0511FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_sv (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, value_bpa DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_610EDC94584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, upline_id INT DEFAULT NULL, sponsor_id INT DEFAULT NULL, tree_root INT DEFAULT NULL, membership_id INT NOT NULL, next_membership_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, cni INT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, entry_date DATETIME NOT NULL, position VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, state VARCHAR(255) DEFAULT \'Actif\' NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', activated TINYINT(1) NOT NULL, date_activation DATETIME DEFAULT NULL, expired TINYINT(1) DEFAULT \'0\' NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, to_upgrade TINYINT(1) DEFAULT \'0\' NOT NULL, mobile_phone VARCHAR(255) NOT NULL, served TINYINT(1) DEFAULT \'0\' NOT NULL, title VARCHAR(255) NOT NULL, date_of_birth DATE DEFAULT NULL, document_type VARCHAR(255) NOT NULL, next_of_kin VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8D93D649207AA998 (upline_id), INDEX IDX_8D93D64912F7FB51 (sponsor_id), INDEX IDX_8D93D649A977936C (tree_root), INDEX IDX_8D93D6491FB354CD (membership_id), INDEX IDX_8D93D6495FBB50AB (next_membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameter_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, removed TINYINT(1) NOT NULL, record_date DATETIME NOT NULL, deactivated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(180) NOT NULL, name VARCHAR(255) NOT NULL, coefficent INT NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_86FFD28577153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership_sv (id INT AUTO_INCREMENT NOT NULL, membership_id INT NOT NULL, sv_groupe DOUBLE PRECISION NOT NULL, sv_product DOUBLE PRECISION NOT NULL, state TINYINT(1) NOT NULL, started DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_5C27DF5E1FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsoring_bonus (id INT AUTO_INCREMENT NOT NULL, sponsor_id INT NOT NULL, paid TINYINT(1) NOT NULL, date_activation DATETIME NOT NULL, date_bonus_paid DATETIME DEFAULT NULL, membership VARCHAR(255) NOT NULL, sponsorised VARCHAR(255) NOT NULL, value DOUBLE PRECISION DEFAULT NULL, INDEX IDX_8F83DA2412F7FB51 (sponsor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_month_carry_over (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, co DOUBLE PRECISION NOT NULL, old_co DOUBLE PRECISION NOT NULL, bonus_groupe DOUBLE PRECISION NOT NULL, lft DOUBLE PRECISION NOT NULL, rgt DOUBLE PRECISION NOT NULL, position VARCHAR(20) NOT NULL, old_position VARCHAR(20) NOT NULL, month VARCHAR(20) NOT NULL, record_date DATETIME NOT NULL, used TINYINT(1) NOT NULL, INDEX IDX_A89CBAAAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo_pack_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, promo_id INT DEFAULT NULL, quantity INT NOT NULL, quantity_for_sv INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_700553C74584665A (product_id), INDEX IDX_700553C7D0C07AFF (promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE command_products (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, command_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_8A5683CB4584665A (product_id), INDEX IDX_8A5683CB33E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle (id INT AUTO_INCREMENT NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_distributor_price (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, applying_date DATETIME NOT NULL, expiration_date DATETIME DEFAULT NULL, INDEX IDX_2495CCA44584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_client_price (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, applying_date DATETIME NOT NULL, expiration_date DATETIME DEFAULT NULL, INDEX IDX_E4019C054584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership_cost (id INT AUTO_INCREMENT NOT NULL, membership_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, state TINYINT(1) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_6319A1371FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_command_pack_promo (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, pack_id INT NOT NULL, quantity INT NOT NULL, date_command DATETIME NOT NULL, date_update_command DATETIME DEFAULT NULL, delivered TINYINT(1) NOT NULL, INDEX IDX_39F61EB67597D3FE (member_id), INDEX IDX_39F61EB61919B217 (pack_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pack_promo (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, started TINYINT(1) NOT NULL, ended TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_commands (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, code VARCHAR(255) NOT NULL, motif VARCHAR(255) NOT NULL, date_command DATETIME NOT NULL, date_command_update DATETIME DEFAULT NULL, delivered TINYINT(1) NOT NULL, INDEX IDX_7AB3F72CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership_subscription (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, membership_id INT NOT NULL, state TINYINT(1) NOT NULL, upgraded TINYINT(1) DEFAULT NULL, paid TINYINT(1) NOT NULL, paid_at DATETIME DEFAULT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_57FD1E17597D3FE (member_id), INDEX IDX_57FD1E11FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE membership_bonus_pourcentage ADD CONSTRAINT FK_6694E0511FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE product_sv ADD CONSTRAINT FK_610EDC94584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649207AA998 FOREIGN KEY (upline_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64912F7FB51 FOREIGN KEY (sponsor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A977936C FOREIGN KEY (tree_root) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495FBB50AB FOREIGN KEY (next_membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE membership_sv ADD CONSTRAINT FK_5C27DF5E1FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE sponsoring_bonus ADD CONSTRAINT FK_8F83DA2412F7FB51 FOREIGN KEY (sponsor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_month_carry_over ADD CONSTRAINT FK_A89CBAAAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE promo_pack_product ADD CONSTRAINT FK_700553C74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE promo_pack_product ADD CONSTRAINT FK_700553C7D0C07AFF FOREIGN KEY (promo_id) REFERENCES pack_promo (id)');
        $this->addSql('ALTER TABLE command_products ADD CONSTRAINT FK_8A5683CB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE command_products ADD CONSTRAINT FK_8A5683CB33E1689A FOREIGN KEY (command_id) REFERENCES user_commands (id)');
        $this->addSql('ALTER TABLE product_distributor_price ADD CONSTRAINT FK_2495CCA44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_client_price ADD CONSTRAINT FK_E4019C054584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE membership_cost ADD CONSTRAINT FK_6319A1371FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE user_command_pack_promo ADD CONSTRAINT FK_39F61EB67597D3FE FOREIGN KEY (member_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_command_pack_promo ADD CONSTRAINT FK_39F61EB61919B217 FOREIGN KEY (pack_id) REFERENCES pack_promo (id)');
        $this->addSql('ALTER TABLE user_commands ADD CONSTRAINT FK_7AB3F72CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membership_subscription ADD CONSTRAINT FK_57FD1E17597D3FE FOREIGN KEY (member_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membership_subscription ADD CONSTRAINT FK_57FD1E11FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649207AA998');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64912F7FB51');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A977936C');
        $this->addSql('ALTER TABLE sponsoring_bonus DROP FOREIGN KEY FK_8F83DA2412F7FB51');
        $this->addSql('ALTER TABLE user_month_carry_over DROP FOREIGN KEY FK_A89CBAAAA76ED395');
        $this->addSql('ALTER TABLE user_command_pack_promo DROP FOREIGN KEY FK_39F61EB67597D3FE');
        $this->addSql('ALTER TABLE user_commands DROP FOREIGN KEY FK_7AB3F72CA76ED395');
        $this->addSql('ALTER TABLE membership_subscription DROP FOREIGN KEY FK_57FD1E17597D3FE');
        $this->addSql('ALTER TABLE membership_bonus_pourcentage DROP FOREIGN KEY FK_6694E0511FB354CD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491FB354CD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495FBB50AB');
        $this->addSql('ALTER TABLE membership_sv DROP FOREIGN KEY FK_5C27DF5E1FB354CD');
        $this->addSql('ALTER TABLE membership_cost DROP FOREIGN KEY FK_6319A1371FB354CD');
        $this->addSql('ALTER TABLE membership_subscription DROP FOREIGN KEY FK_57FD1E11FB354CD');
        $this->addSql('ALTER TABLE product_sv DROP FOREIGN KEY FK_610EDC94584665A');
        $this->addSql('ALTER TABLE promo_pack_product DROP FOREIGN KEY FK_700553C74584665A');
        $this->addSql('ALTER TABLE command_products DROP FOREIGN KEY FK_8A5683CB4584665A');
        $this->addSql('ALTER TABLE product_distributor_price DROP FOREIGN KEY FK_2495CCA44584665A');
        $this->addSql('ALTER TABLE product_client_price DROP FOREIGN KEY FK_E4019C054584665A');
        $this->addSql('ALTER TABLE promo_pack_product DROP FOREIGN KEY FK_700553C7D0C07AFF');
        $this->addSql('ALTER TABLE user_command_pack_promo DROP FOREIGN KEY FK_39F61EB61919B217');
        $this->addSql('ALTER TABLE command_products DROP FOREIGN KEY FK_8A5683CB33E1689A');
        $this->addSql('DROP TABLE membership_bonus_pourcentage');
        $this->addSql('DROP TABLE product_sv');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE parameter_config');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP TABLE membership_sv');
        $this->addSql('DROP TABLE sponsoring_bonus');
        $this->addSql('DROP TABLE user_month_carry_over');
        $this->addSql('DROP TABLE promo_pack_product');
        $this->addSql('DROP TABLE command_products');
        $this->addSql('DROP TABLE cycle');
        $this->addSql('DROP TABLE product_distributor_price');
        $this->addSql('DROP TABLE product_client_price');
        $this->addSql('DROP TABLE membership_cost');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user_command_pack_promo');
        $this->addSql('DROP TABLE pack_promo');
        $this->addSql('DROP TABLE user_commands');
        $this->addSql('DROP TABLE membership_subscription');
    }
}
