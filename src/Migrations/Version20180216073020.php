<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180216073020 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE office DROP FOREIGN KEY FK_74516B02A76ED395');
        $this->addSql('DROP INDEX UNIQ_74516B02A76ED395 ON office');
        $this->addSql('ALTER TABLE office DROP user_id');
        $this->addSql('ALTER TABLE user ADD office_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FFA0C224 FOREIGN KEY (office_id) REFERENCES office (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649FFA0C224 ON user (office_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE office ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B02A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_74516B02A76ED395 ON office (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FFA0C224');
        $this->addSql('DROP INDEX IDX_8D93D649FFA0C224 ON user');
        $this->addSql('ALTER TABLE user DROP office_id');
    }
}
