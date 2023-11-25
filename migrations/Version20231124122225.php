<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231124122225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordering ADD cart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ordering ADD CONSTRAINT FK_7B3133671AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('CREATE INDEX IDX_7B3133671AD5CDBF ON ordering (cart_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordering DROP FOREIGN KEY FK_7B3133671AD5CDBF');
        $this->addSql('DROP INDEX IDX_7B3133671AD5CDBF ON ordering');
        $this->addSql('ALTER TABLE ordering DROP cart_id');
    }
}
