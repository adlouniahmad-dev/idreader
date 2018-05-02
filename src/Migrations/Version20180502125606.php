<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180502125606 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE logs_history ADD log_id INT DEFAULT NULL, ADD visitor_name VARCHAR(255) DEFAULT NULL, ADD time_entered TIME DEFAULT NULL, ADD time_left_from_office TIME DEFAULT NULL, ADD time_exit TIME DEFAULT NULL, ADD date_entered DATE DEFAULT NULL, ADD building VARCHAR(255) DEFAULT NULL, ADD office_name VARCHAR(255) DEFAULT NULL, ADD guard_check_in VARCHAR(255) DEFAULT NULL, ADD guard_check_out VARCHAR(255) DEFAULT NULL, ADD gate_check_in VARCHAR(255) DEFAULT NULL, ADD gate_check_out VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE logs_history DROP log_id, DROP visitor_name, DROP time_entered, DROP time_left_from_office, DROP time_exit, DROP date_entered, DROP building, DROP office_name, DROP guard_check_in, DROP guard_check_out, DROP gate_check_in, DROP gate_check_out');
    }
}
