<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210817234912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD code_distributor VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE command_products ADD item_distributor_price DOUBLE PRECISION DEFAULT NULL, ADD item_client_price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user_commands ADD is_distributor TINYINT(1) DEFAULT \'1\', ADD total_distributor_price DOUBLE PRECISION DEFAULT NULL, ADD total_client_price DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE command_products DROP item_distributor_price, DROP item_client_price');
        $this->addSql('ALTER TABLE user DROP code_distributor');
        $this->addSql('ALTER TABLE user_commands DROP is_distributor, DROP total_distributor_price, DROP total_client_price');
    }
}
