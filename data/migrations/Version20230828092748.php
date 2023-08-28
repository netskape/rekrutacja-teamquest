<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828092748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tworzenie tabeli users';
    }

    public function up(Schema $schema) : void
    {
        // Tworzenie nowej tabeli
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('password', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP','notnull' => false]);
        $table->addColumn('updated_at', 'datetime',['notnull' => false]);
        $table->addColumn('change_password_required', 'boolean', ['default' => true,'notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('users');
    }
}
