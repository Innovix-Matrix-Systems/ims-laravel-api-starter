<?php

namespace App\Checks;

use Illuminate\Database\Migrations\Migrator;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class DatabaseMigrationCheck extends Check
{
    /** @var Migrator */
    protected $migrator;

    public function run(): Result
    {
        $pendingMigrations = $this->getPendingMigrations();

        if (count($pendingMigrations) === 0) {
            return Result::make()->ok('All migrations are up to date.');
        }

        return Result::make()->warning(
            'The following migrations are pending: ' . implode(', ', $pendingMigrations)
        );
    }

    /** @return Migrator */
    protected function getMigrator()
    {
        if (is_null($this->migrator)) {
            $this->migrator = app('migrator');
        }

        return $this->migrator;
    }

    /** @return string */
    protected function getMigrationPath()
    {
        return database_path() . DIRECTORY_SEPARATOR . 'migrations';
    }

    private function getCompletedMigrations()
    {
        if (! $this->getMigrator()->repositoryExists()) {
            return [];
        }

        return $this->getMigrator()->getRepository()->getRan();
    }

    private function getPendingMigrations()
    {
        $files = $this->getMigrator()->getMigrationFiles($this->getMigrationPath());

        return array_diff(array_keys($files), $this->getCompletedMigrations());
    }
}
