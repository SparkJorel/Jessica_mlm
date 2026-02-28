<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200624161938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_binary_cycle (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cycle_id INT NOT NULL, binaire DOUBLE PRECISION NOT NULL, INDEX IDX_8C26DEDAA76ED395 (user_id), INDEX IDX_8C26DEDA5EC1162 (cycle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_binary_cycle ADD CONSTRAINT FK_8C26DEDAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_binary_cycle ADD CONSTRAINT FK_8C26DEDA5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE user_bonus_special ADD started_at DATETIME NOT NULL, ADD ended_at DATETIME DEFAULT NULL, ADD status TINYINT(1) NOT NULL, ADD promo TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_binary_cycle');
        $this->addSql('ALTER TABLE user_bonus_special DROP started_at, DROP ended_at, DROP status, DROP promo');
    }
}
