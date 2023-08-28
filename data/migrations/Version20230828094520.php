<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828094520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Wstawiam rekordy do tabeli users';
    }

    public function up(Schema $schema): void
    {
        //hasło bez funkcji hasuzkącej: test
        $this->addSql("INSERT INTO users (email, password) VALUES ('kzubkowicz@gmail.com', 'ee26b0dd4af7e749aa1a8ee3c10ae9923f618980772e473f8819a5d4940e0db27ac185f8a0e1d5f84f88bc887fd67b143732c304cc5fa9ad8e6f57f50028a8ff')");


    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE from users WHERE email = 'kzubkowicz@gmail.com'");
    }
}
