<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200624102718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bonus_special (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, cap1 INT NOT NULL, cap2 INT NOT NULL, description LONGTEXT DEFAULT NULL, image_file VARCHAR(255) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_bonus_special (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, bonus_id INT NOT NULL, month VARCHAR(255) NOT NULL, recorded_at DATETIME NOT NULL, INDEX IDX_537E7ABBA76ED395 (user_id), INDEX IDX_537E7ABB69545666 (bonus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_bonus_special ADD CONSTRAINT FK_537E7ABBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_bonus_special ADD CONSTRAINT FK_537E7ABB69545666 FOREIGN KEY (bonus_id) REFERENCES bonus_special (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_bonus_special DROP FOREIGN KEY FK_537E7ABB69545666');
        $this->addSql('DROP TABLE bonus_special');
        $this->addSql('DROP TABLE user_bonus_special');
    }
}
