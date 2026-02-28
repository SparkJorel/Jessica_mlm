<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211007020118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commission_indirect_bonus (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lvl INT NOT NULL, reason VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, month VARCHAR(20) NOT NULL, year VARCHAR(50) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, INDEX IDX_1B298D9DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indirect_bonus_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, lvl INT NOT NULL, INDEX IDX_AB372DA74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indirect_bonus_membership (id INT AUTO_INCREMENT NOT NULL, membership_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, lvl INT NOT NULL, INDEX IDX_E19AC5411FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE summary_commission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, reason VARCHAR(255) NOT NULL, amount DOUBLE PRECISION DEFAULT NULL, started_at DATETIME NOT NULL, month VARCHAR(20) NOT NULL, year VARCHAR(50) NOT NULL, ended_at DATETIME NOT NULL, status TINYINT(1) DEFAULT \'0\', INDEX IDX_3CE40090A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commission_indirect_bonus ADD CONSTRAINT FK_1B298D9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE indirect_bonus_product ADD CONSTRAINT FK_AB372DA74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE indirect_bonus_membership ADD CONSTRAINT FK_E19AC5411FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE summary_commission ADD CONSTRAINT FK_3CE40090A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD user_grade_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491E9445A8 FOREIGN KEY (user_grade_id) REFERENCES grade (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6491E9445A8 ON user (user_grade_id)');
        $this->addSql('ALTER TABLE membership CHANGE membership_product_sv membership_product_cote DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE user_paid_bonus ADD year VARCHAR(255) DEFAULT NULL, ADD started_at DATETIME DEFAULT NULL, ADD ended_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_month_carry_over ADD old_position VARCHAR(20) DEFAULT NULL, ADD started_at DATETIME DEFAULT NULL, ADD ended_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE cycle ADD weekly TINYINT(1) DEFAULT \'0\', ADD auto_save TINYINT(1) DEFAULT \'0\', ADD binary_saved TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE membership_subscription ADD total_svbinaire DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE commission_indirect_bonus');
        $this->addSql('DROP TABLE indirect_bonus_product');
        $this->addSql('DROP TABLE indirect_bonus_membership');
        $this->addSql('DROP TABLE summary_commission');
        $this->addSql('ALTER TABLE cycle DROP weekly, DROP auto_save, DROP binary_saved');
        $this->addSql('ALTER TABLE membership CHANGE membership_product_cote membership_product_sv DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE membership_subscription DROP total_svbinaire');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491E9445A8');
        $this->addSql('DROP INDEX IDX_8D93D6491E9445A8 ON user');
        $this->addSql('ALTER TABLE user DROP user_grade_id');
        $this->addSql('ALTER TABLE user_month_carry_over DROP old_position, DROP started_at, DROP ended_at');
        $this->addSql('ALTER TABLE user_paid_bonus DROP year, DROP started_at, DROP ended_at');
    }
}
