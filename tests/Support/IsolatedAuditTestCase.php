<?php

namespace Tests\Support;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

abstract class IsolatedAuditTestCase extends TestCase
{
    public function createApplication()
    {
        // Never load the project's credentials or cached production configuration.
        foreach ([
            'APP_ENV' => 'testing',
            'APP_CONFIG_CACHE' => __DIR__.'/missing-config.php',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'CACHE_STORE' => 'array',
            'SESSION_DRIVER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
        ] as $key => $value) {
            putenv($key.'='.$value);
            $_ENV[$key] = $_SERVER[$key] = $value;
        }
        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->loadEnvironmentFrom('.env.audit-tests');
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }


    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Http::fake();
        Queue::fake();
        Mail::fake();
        Notification::fake();
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
    }
}
