<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180120135734 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blacklist (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, date_added DATETIME NOT NULL, UNIQUE INDEX UNIQ_3B17538570BEE6D (visitor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, start_floor INT NOT NULL, end_floor INT NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, mac_address VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gate (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_B82B98944D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guard (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, $device_id INT DEFAULT NULL, is_activated VARBINARY(255) NOT NULL, is_blocked VARBINARY(255) NOT NULL, UNIQUE INDEX UNIQ_AF1213CCA76ED395 (user_id), UNIQUE INDEX UNIQ_AF1213CCD79118A6 ($device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, time_entered TIME NOT NULL, time_left TIME NOT NULL, estimated_time CHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', date_created DATE NOT NULL, is_suspicious VARBINARY(255) NOT NULL, INDEX IDX_8F3F68C570BEE6D (visitor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_gate (id INT AUTO_INCREMENT NOT NULL, gate_id INT DEFAULT NULL, log_id INT DEFAULT NULL, time TIME NOT NULL, INDEX IDX_A94576E1897F2CF6 (gate_id), INDEX IDX_A94576E1EA675D86 (log_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_guard (id INT AUTO_INCREMENT NOT NULL, guard_id INT DEFAULT NULL, log_id INT DEFAULT NULL, time TIME NOT NULL, INDEX IDX_8F6CF8916CA29A61 (guard_id), INDEX IDX_8F6CF891EA675D86 (log_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE office (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, user_id INT DEFAULT NULL, office_nb VARCHAR(255) NOT NULL, floor_nb INT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_74516B024D2A7E12 (building_id), UNIQUE INDEX UNIQ_74516B02A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, role_name VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, guard_id INT DEFAULT NULL, gate_id INT DEFAULT NULL, INDEX IDX_5A3811FB6CA29A61 (guard_id), INDEX IDX_5A3811FB897F2CF6 (gate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shift (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(8) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_51498A8EA76ED395 (user_id), INDEX IDX_51498A8ED60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitor (id INT AUTO_INCREMENT NOT NULL, ssn VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, nationality VARCHAR(255) NOT NULL, document_type VARCHAR(255) NOT NULL, has_card VARBINARY(255) NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blacklist ADD CONSTRAINT FK_3B17538570BEE6D FOREIGN KEY (visitor_id) REFERENCES visitor (id)');
        $this->addSql('ALTER TABLE gate ADD CONSTRAINT FK_B82B98944D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE guard ADD CONSTRAINT FK_AF1213CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE guard ADD CONSTRAINT FK_AF1213CCD79118A6 FOREIGN KEY ($device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C570BEE6D FOREIGN KEY (visitor_id) REFERENCES visitor (id)');
        $this->addSql('ALTER TABLE log_gate ADD CONSTRAINT FK_A94576E1897F2CF6 FOREIGN KEY (gate_id) REFERENCES gate (id)');
        $this->addSql('ALTER TABLE log_gate ADD CONSTRAINT FK_A94576E1EA675D86 FOREIGN KEY (log_id) REFERENCES log (id)');
        $this->addSql('ALTER TABLE log_guard ADD CONSTRAINT FK_8F6CF8916CA29A61 FOREIGN KEY (guard_id) REFERENCES guard (id)');
        $this->addSql('ALTER TABLE log_guard ADD CONSTRAINT FK_8F6CF891EA675D86 FOREIGN KEY (log_id) REFERENCES log (id)');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B024D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B02A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB6CA29A61 FOREIGN KEY (guard_id) REFERENCES guard (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB897F2CF6 FOREIGN KEY (gate_id) REFERENCES gate (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gate DROP FOREIGN KEY FK_B82B98944D2A7E12');
        $this->addSql('ALTER TABLE office DROP FOREIGN KEY FK_74516B024D2A7E12');
        $this->addSql('ALTER TABLE guard DROP FOREIGN KEY FK_AF1213CCD79118A6');
        $this->addSql('ALTER TABLE log_gate DROP FOREIGN KEY FK_A94576E1897F2CF6');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB897F2CF6');
        $this->addSql('ALTER TABLE log_guard DROP FOREIGN KEY FK_8F6CF8916CA29A61');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB6CA29A61');
        $this->addSql('ALTER TABLE log_gate DROP FOREIGN KEY FK_A94576E1EA675D86');
        $this->addSql('ALTER TABLE log_guard DROP FOREIGN KEY FK_8F6CF891EA675D86');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8ED60322AC');
        $this->addSql('ALTER TABLE blacklist DROP FOREIGN KEY FK_3B17538570BEE6D');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C570BEE6D');
        $this->addSql('DROP TABLE blacklist');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE gate');
        $this->addSql('DROP TABLE guard');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE log_gate');
        $this->addSql('DROP TABLE log_guard');
        $this->addSql('DROP TABLE office');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE shift');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE visitor');
    }
}
