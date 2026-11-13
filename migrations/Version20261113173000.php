<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261113173000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tworzy schemat Pogodynki lub uzupełnia pomiary o godzinę, wilgotność i ciśnienie.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof SQLitePlatform, 'Ta migracja jest przeznaczona dla SQLite.');

        $this->addSql('CREATE TABLE IF NOT EXISTS location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(2) NOT NULL, latitude NUMERIC(10, 7) NOT NULL, longitude NUMERIC(10, 7) NOT NULL)');
        $this->addSql('CREATE TABLE IF NOT EXISTS user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_IDENTIFIER_USERNAME ON user (username)');
        $this->addSql('CREATE TABLE IF NOT EXISTS messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');

        $measurementExists = $schema->hasTable('measurement');

        if ($measurementExists && $schema->getTable('measurement')->hasColumn('pressure_hpa')) {
            return;
        }

        // SQLite wymaga odtworzenia tabeli przy zmianie typu istniejących kolumn.
        if ($measurementExists) {
            $this->addSql('ALTER TABLE measurement RENAME TO measurement_before_weather_fields');
        }

        $this->addSql('CREATE TABLE measurement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, celsius NUMERIC(5, 2) NOT NULL, humidity INTEGER DEFAULT NULL, pressure_hpa NUMERIC(6, 2) DEFAULT NULL, location_id INTEGER NOT NULL, CONSTRAINT FK_2CE0D81164D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');

        if ($measurementExists) {
            $this->addSql(<<<'SQL'
                INSERT INTO measurement (id, date, celsius, location_id)
                SELECT id, CASE WHEN length(date) = 10 THEN date || ' 00:00:00' ELSE date END, celsius, location_id
                FROM measurement_before_weather_fields
                SQL);
            $this->addSql('DROP TABLE measurement_before_weather_fields');
        }

        $this->addSql('CREATE INDEX IDX_2CE0D81164D218E ON measurement (location_id)');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Cofnięcie migracji mogłoby usunąć zapisane godziny, wilgotność i ciśnienie.');
    }
}
