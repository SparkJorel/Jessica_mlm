<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825102930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_month_carry_over ADD sv_gain DOUBLE PRECISION DEFAULT NULL, ADD gain DOUBLE PRECISION DEFAULT NULL, ADD left_side_sponsoring_sv DOUBLE PRECISION DEFAULT NULL, ADD left_side_achat_sv DOUBLE PRECISION DEFAULT NULL, ADD left_side_total_sv DOUBLE PRECISION DEFAULT NULL, ADD right_side_sponsoring_sv DOUBLE PRECISION DEFAULT NULL, ADD right_side_achat_sv DOUBLE PRECISION DEFAULT NULL, ADD right_side_total_sv DOUBLE PRECISION DEFAULT NULL, ADD old_co DOUBLE PRECISION DEFAULT NULL, ADD left_or_right_side_new_total_sv DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_month_carry_over DROP sv_gain, DROP gain, DROP left_side_sponsoring_sv, DROP left_side_achat_sv, DROP left_side_total_sv, DROP right_side_sponsoring_sv, DROP right_side_achat_sv, DROP right_side_total_sv, DROP old_co, DROP left_or_right_side_new_total_sv');
    }
}
