<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SmokeTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:smoke-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run smoke tests to verify production readiness';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Running Production Readiness Smoke Tests...');
        $this->newLine();

        $passed = 0;
        $failed = 0;

        // Test 1: Database Connection
        if ($this->testDatabaseConnection()) {
            $passed++;
        } else {
            $failed++;
        }

        // Test 2: Redis Connection
        if ($this->testRedisConnection()) {
            $passed++;
        } else {
            $failed++;
        }

        // Test 3: Environment Variables
        if ($this->testEnvironmentVariables()) {
            $passed++;
        } else {
            $failed++;
        }

        // Test 4: Migrations Status
        if ($this->testMigrations()) {
            $passed++;
        } else {
            $failed++;
        }

        // Test 5: Storage Permissions
        if ($this->testStoragePermissions()) {
            $passed++;
        } else {
            $failed++;
        }

        // Test 6: Firebase Config
        if ($this->testFirebaseConfig()) {
            $passed++;
        } else {
            $failed++;
        }

        $this->newLine();
        $this->info("✅ Passed: {$passed}");
        if ($failed > 0) {
            $this->error("❌ Failed: {$failed}");
            return 1;
        }

        $this->info('🎉 All smoke tests passed! System is production-ready.');
        return 0;
    }

    protected function testDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            $this->line('✓ Database connection: OK');
            return true;
        } catch (\Exception $e) {
            $this->error('✗ Database connection: FAILED - ' . $e->getMessage());
            return false;
        }
    }

    protected function testRedisConnection()
    {
        try {
            \Redis::connection()->ping();
            $this->line('✓ Redis connection: OK');
            return true;
        } catch (\Exception $e) {
            $this->error('✗ Redis connection: FAILED - ' . $e->getMessage());
            return false;
        }
    }

    protected function testEnvironmentVariables()
    {
        $required = [
            'APP_KEY',
            'DB_DATABASE',
            'PHONEPE_MERCHANT_ID',
            'PHONEPE_SALT_KEY',
            'FIREBASE_CREDENTIALS'
        ];

        $missing = [];
        foreach ($required as $var) {
            if (!config(str_replace('_', '.', strtolower($var))) && !env($var)) {
                $missing[] = $var;
            }
        }

        if (empty($missing)) {
            $this->line('✓ Environment variables: OK');
            return true;
        }

        $this->error('✗ Environment variables: MISSING - ' . implode(', ', $missing));
        return false;
    }

    protected function testMigrations()
    {
        try {
            $exitCode = \Artisan::call('migrate:status');
            if ($exitCode === 0) {
                $this->line('✓ Migrations: OK');
                return true;
            }
            $this->error('✗ Migrations: PENDING');
            return false;
        } catch (\Exception $e) {
            $this->error('✗ Migrations: ERROR - ' . $e->getMessage());
            return false;
        }
    }

    protected function testStoragePermissions()
    {
        $paths = [storage_path('logs'), storage_path('framework/cache')];

        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $this->error("✗ Storage permissions: NOT WRITABLE - {$path}");
                return false;
            }
        }

        $this->line('✓ Storage permissions: OK');
        return true;
    }

    protected function testFirebaseConfig()
    {
        $credPath = config('firebase.credentials.file');

        if (!$credPath || !file_exists($credPath)) {
            $this->error('✗ Firebase config: CREDENTIALS FILE NOT FOUND');
            return false;
        }

        $this->line('✓ Firebase config: OK');
        return true;
    }
}
