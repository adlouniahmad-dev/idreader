<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180419143232 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE logs_search_history (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, visitor_name VARCHAR(255) NOT NULL, building VARCHAR(255) NOT NULL, office VARCHAR(255) NOT NULL, entrance_gate VARCHAR(255) DEFAULT NULL, exit_gate VARCHAR(255) DEFAULT NULL, entrance_guard VARCHAR(255) DEFAULT NULL, exit_guard VARCHAR(255) DEFAULT NULL, date_from VARCHAR(255) NOT NULL, date_to VARCHAR(255) NOT NULL, time_entered_from VARCHAR(255) DEFAULT NULL, time_entered_to VARCHAR(255) DEFAULT NULL, time_exit_from VARCHAR(255) DEFAULT NULL, time_exit_to VARCHAR(255) DEFAULT NULL, time_left_from_office_from VARCHAR(255) DEFAULT NULL, time_left_from_office_to VARCHAR(255) DEFAULT NULL, date_time_search DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE logs_search_history');
    }
}
