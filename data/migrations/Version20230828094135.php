<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828094135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tworzenie triggerów do tabeli users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TRIGGER user_after_insert AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
               INSERT INTO password_history (user_id, password) VALUES (NEW.id, NEW.password);
            END;
        ');
        $this->addSql('
            CREATE TRIGGER user_before_update BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                 SET NEW.updated_at = CURRENT_TIMESTAMP();
            END;
        ');
        $this->addSql('
            CREATE TRIGGER user_after_update AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                 IF NEW.password <> OLD.password THEN
                    INSERT INTO password_history (user_id, password) VALUES (NEW.id,NEW.password);
                 END IF;
            END;
        ');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS user_after_insert');
        $this->addSql('DROP TRIGGER IF EXISTS user_before_update');
        $this->addSql('DROP TRIGGER IF EXISTS user_after_update');

    }
}
