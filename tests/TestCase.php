<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Safety Gate: Abort immediately if environment is not 'testing'
        if (app()->environment() !== 'testing') {
            throw new \RuntimeException(
                "CRITICAL DATABASE SAFETY GATE: Tests cannot run in environment '" . app()->environment() . "'. Expected 'testing'."
            );
        }

        // 2. Enforce isolated testing database connection
        if (!($this instanceof \Tests\Support\IsolatedAuditTestCase)) {
            $testDbPath = database_path('testing.sqlite');
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => $testDbPath,
            ]);
            DB::purge('mysql');
            DB::setDefaultConnection('sqlite');
        }

        // 3. Safety Gate: Verify that active DB does NOT match the local primary app DB
        $currentConnection = DB::getDefaultConnection();
        $currentDb = (string)config("database.connections.{$currentConnection}.database");

        if (strtolower(basename($currentDb)) === 'cleanstation' || strtolower($currentDb) === 'cleanstation' || $currentConnection === 'mysql') {
            throw new \RuntimeException(
                "CRITICAL DATABASE SAFETY GATE: Tests attempted to connect to primary local database '{$currentDb}' on connection '{$currentConnection}'. Aborted immediately to protect primary data."
            );
        }

        // Mark as migrated so RefreshDatabase uses transactions without wiping pre-migrated sqlite tables
        RefreshDatabaseState::$migrated = true;
    }
}
