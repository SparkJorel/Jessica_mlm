<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200629173133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bonus_special ADD grade_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bonus_special ADD CONSTRAINT FK_BDF0D7EBFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('CREATE INDEX IDX_BDF0D7EBFE19A1A8 ON bonus_special (grade_id)');
        $this->addSql('ALTER TABLE user_bonus_special CHANGE month month VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bonus_special DROP FOREIGN KEY FK_BDF0D7EBFE19A1A8');
        $this->addSql('DROP INDEX IDX_BDF0D7EBFE19A1A8 ON bonus_special');
        $this->addSql('ALTER TABLE bonus_special DROP grade_id');
        $this->addSql('ALTER TABLE user_bonus_special CHANGE month month VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
