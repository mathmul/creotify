<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251026093814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change order_item.item_id type from VARCHAR to INT';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_item ALTER item_id TYPE INT USING item_id::integer');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_item ALTER item_id TYPE VARCHAR(20)');
    }
}
