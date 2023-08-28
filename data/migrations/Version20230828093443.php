<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828093443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tworze tabeli password_history';
    }

    public function up(Schema $schema): void
    {
        // Tworzenie nowej tabeli
        $table = $schema->createTable('password_history');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('user_id', 'integer');
        $table->addColumn('password', 'string', ['length' => 255]);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->setPrimaryKey(['id']);

    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('password_history');

    }
}
