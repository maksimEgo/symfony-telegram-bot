<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240905154006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE interfaces_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE keyboards_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE interfaces (id INT NOT NULL, message_id INT NOT NULL, keyboard_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E358958E5E237E06 ON interfaces (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E358958E537A1329 ON interfaces (message_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E358958EF17292C6 ON interfaces (keyboard_id)');
        $this->addSql('CREATE TABLE keyboards (id INT NOT NULL, buttons JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messages (id INT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE interfaces ADD CONSTRAINT FK_E358958E537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE interfaces ADD CONSTRAINT FK_E358958EF17292C6 FOREIGN KEY (keyboard_id) REFERENCES keyboards (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE interfaces_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE keyboards_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE messages_id_seq CASCADE');
        $this->addSql('ALTER TABLE interfaces DROP CONSTRAINT FK_E358958E537A1329');
        $this->addSql('ALTER TABLE interfaces DROP CONSTRAINT FK_E358958EF17292C6');
        $this->addSql('DROP TABLE interfaces');
        $this->addSql('DROP TABLE keyboards');
        $this->addSql('DROP TABLE messages');
    }
}
