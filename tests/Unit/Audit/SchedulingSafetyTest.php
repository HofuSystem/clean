<?php

namespace Tests\Unit\Audit;

use PHPUnit\Framework\TestCase;

class SchedulingSafetyTest extends TestCase
{
    public function test_queue_worker_schedule_prevents_overlapping_workers(): void
    {
        $schedule = file_get_contents(dirname(__DIR__, 3) . '/routes/console.php');

        $this->assertStringContainsString("Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping(10);", $schedule);
    }

    public function test_queue_restart_remains_periodic(): void
    {
        $schedule = file_get_contents(dirname(__DIR__, 3) . '/routes/console.php');

        $this->assertStringContainsString("Schedule::command('queue:restart')->everyFiveMinutes();", $schedule);
    }
}
