<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619105157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD geo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9FA49D0B FOREIGN KEY (geo_id) REFERENCES geo (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9FA49D0B ON users (geo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9FA49D0B');
        $this->addSql('DROP INDEX IDX_1483A5E9FA49D0B ON users');
        $this->addSql('ALTER TABLE users DROP geo_id');
    }
}