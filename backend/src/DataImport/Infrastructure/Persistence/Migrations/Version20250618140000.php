<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250618140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create DataImport module tables for import jobs and eToro statement data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE import_jobs (id UUID NOT NULL, import_type VARCHAR(50) NOT NULL, file_path VARCHAR(512) NOT NULL, original_filename VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, total_chunks INT NOT NULL, processed_chunks INT NOT NULL, errors JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE etoro_closed_positions (id UUID NOT NULL, import_job_id UUID NOT NULL, row_hash VARCHAR(64) NOT NULL, row_number INT NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_etoro_closed_positions_job_hash ON etoro_closed_positions (import_job_id, row_hash)');
        $this->addSql('CREATE TABLE etoro_account_activities (id UUID NOT NULL, import_job_id UUID NOT NULL, row_hash VARCHAR(64) NOT NULL, row_number INT NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_etoro_account_activities_job_hash ON etoro_account_activities (import_job_id, row_hash)');
        $this->addSql('CREATE TABLE etoro_dividends (id UUID NOT NULL, import_job_id UUID NOT NULL, row_hash VARCHAR(64) NOT NULL, row_number INT NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_etoro_dividends_job_hash ON etoro_dividends (import_job_id, row_hash)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE etoro_dividends');
        $this->addSql('DROP TABLE etoro_account_activities');
        $this->addSql('DROP TABLE etoro_closed_positions');
        $this->addSql('DROP TABLE import_jobs');
    }
}
