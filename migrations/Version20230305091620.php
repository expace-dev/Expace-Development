<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305091620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE portfolios ADD projet_id INT NOT NULL, DROP client');
        $this->addSql('ALTER TABLE portfolios ADD CONSTRAINT FK_B81B226FC18272 FOREIGN KEY (projet_id) REFERENCES projets (id)');
        $this->addSql('CREATE INDEX IDX_B81B226FC18272 ON portfolios (projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE portfolios DROP FOREIGN KEY FK_B81B226FC18272');
        $this->addSql('DROP INDEX IDX_B81B226FC18272 ON portfolios');
        $this->addSql('ALTER TABLE portfolios ADD client VARCHAR(255) NOT NULL, DROP projet_id');
    }
}
