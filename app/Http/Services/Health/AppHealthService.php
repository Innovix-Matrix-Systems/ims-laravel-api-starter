<?php

namespace App\Http\Services\Health;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class AppHealthService
{
    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * @return Migrator
     */
    protected function getMigrator()
    {
        if (is_null($this->migrator)) {
            $this->migrator = app('migrator');
        }

        return $this->migrator;
    }

    /**
     * @return string
     */
    protected function getMigrationPath()
    {
        return database_path() . DIRECTORY_SEPARATOR . 'migrations';
    }

    /**
     * Test cache is operational or not
     * @name isCacheTestSuccessful
     *
     * @return boolean
     */
    public function isCacheTestSuccessful()
    {
        $isHealthy = true;
        try {
            //code...
            $cache = Cache::store(config("cache.default"));
            $cache->put('laravel-health-check', 'healthy', Carbon::now()->addMinutes(1));
            $value = $cache->pull('laravel-health-check', 'broken');
            if ($value != 'healthy') {
                $isHealthy = false;
            }
        } catch (\Throwable $th) {
            //throw $th;
            $isHealthy = false;
        }
        return $isHealthy;
    }

    /**
     * Test http request is operational or not
     * @name isHttpTestSuccessful
     *
     * @return boolean
     */
    public function isHttpTestSuccessful()
    {
        $isHealthy = true;
        $url = route('http.test');
        try {
            $request = Request::create($url, 'GET');
            $response = Route::dispatch($request);
        } catch (\Throwable $th) {
            //throw $th;
            $isHealthy = false;
        }
        return $isHealthy;
    }

    /**
     * Test storage and its permission is operational or not
     * @name isStorageTestSuccessful
     *
     * @return boolean
     */
    public function isStorageTestSuccessful()
    {
        $isHealthy = true;
        $uniqueString = uniqid('laravel-health-check_', true);
        try {
            $storage = Storage::disk("health");
            $storage->put($uniqueString, $uniqueString);
            $contents = $storage->get($uniqueString);
            $storage->delete($uniqueString);
        } catch (\Throwable $th) {
            // throw $th;
            $isHealthy = false;
        }
        return $isHealthy;
    }

    /**
    * Test Database connection is successful or not
    * @name isDatabaseTestSuccessful
    *
     * @return boolean
    */
    public function isDatabaseTestSuccessful()
    {
        $isHealthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $th) {
            $isHealthy = false;
        }
        return $isHealthy;
    }

    /**
     * Test Migration is up-todate or not
     * @name isMigrationTestSuccessful
     *
     * @return boolean
     */
    public function isMigrationTestSuccessful()
    {
        $isHealthy = true;
        try {
            //code...
            $pendingMigrations = (array)$this->getPendingMigrations();
            $isHealthy = count($pendingMigrations) === 0;
        } catch (\Throwable $th) {
            //throw $th;
            $isHealthy = false;
        }

        return $isHealthy;
    }

    private function getCompletedMigrations()
    {
        if (!$this->getMigrator()->repositoryExists()) {
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
