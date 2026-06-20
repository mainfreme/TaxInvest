<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250618120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create currency_rates table for CurrencyRates module';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE currency_rates (
            id UUID NOT NULL, 
            base_currency VARCHAR(3) NOT NULL,
            target_currency VARCHAR(3) NOT NULL,
            rate NUMERIC(18, 8) NOT NULL,
            effective_date DATE NOT NULL, 
            source VARCHAR(255) DEFAULT NULL, 
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            PRIMARY KEY(id))
        ');
        $this->addSql('CREATE UNIQUE INDEX uniq_currency_rates_pair_date ON currency_rates (base_currency, target_currency, effective_date)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE currency_rates');
    }
}
