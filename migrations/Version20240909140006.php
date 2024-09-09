<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909140006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX unique_name ON bot (name)');
        $this->addSql('CREATE UNIQUE INDEX unique_token ON bot (token)');
        $this->addSql('CREATE INDEX idx_message_id ON interfaces (message_id)');
        $this->addSql('CREATE INDEX idx_keyboard_id ON interfaces (keyboard_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX unique_name');
        $this->addSql('DROP INDEX unique_token');
        $this->addSql('DROP INDEX idx_message_id');
        $this->addSql('DROP INDEX idx_keyboard_id');
    }
}
