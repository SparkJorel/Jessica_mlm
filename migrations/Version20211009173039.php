<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211009173039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE membership_product (id INT AUTO_INCREMENT NOT NULL, name_id INT NOT NULL, product_id INT NOT NULL, membership_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_5AE792FC71179CD6 (name_id), INDEX IDX_5AE792FC4584665A (product_id), INDEX IDX_5AE792FC1FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE composition_membership_product_name (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE membership_product ADD CONSTRAINT FK_5AE792FC71179CD6 FOREIGN KEY (name_id) REFERENCES composition_membership_product_name (id)');
        $this->addSql('ALTER TABLE membership_product ADD CONSTRAINT FK_5AE792FC4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE membership_product ADD CONSTRAINT FK_5AE792FC1FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE membership_subscription ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE membership_subscription ADD CONSTRAINT FK_57FD1E1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_57FD1E1B03A8386 ON membership_subscription (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_product DROP FOREIGN KEY FK_5AE792FC71179CD6');
        $this->addSql('DROP TABLE membership_product');
        $this->addSql('DROP TABLE composition_membership_product_name');
        $this->addSql('ALTER TABLE membership_subscription DROP FOREIGN KEY FK_57FD1E1B03A8386');
        $this->addSql('DROP INDEX IDX_57FD1E1B03A8386 ON membership_subscription');
        $this->addSql('ALTER TABLE membership_subscription DROP created_by_id');
    }
}
