<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240914173129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD commented_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment DROP name');
        $this->addSql('ALTER TABLE comment DROP email');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C94F6F716 FOREIGN KEY (commented_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526C94F6F716 ON comment (commented_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C94F6F716');
        $this->addSql('DROP INDEX IDX_9474526C94F6F716');
        $this->addSql('ALTER TABLE comment ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment DROP commented_by_id');
    }
}
