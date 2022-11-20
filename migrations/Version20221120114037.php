<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221120114037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'setting question and comment status to be submitted by default.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ALTER status SET DEFAULT \'submitted\'');
        $this->addSql('ALTER TABLE question ALTER status SET DEFAULT \'submitted\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE question ALTER status DROP DEFAULT');
    }
}
