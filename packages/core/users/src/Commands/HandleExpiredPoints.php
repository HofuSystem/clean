<?php

namespace Core\Users\Commands;

use Illuminate\Console\Command;

class HandleExpiredPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-expired-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Point expiry is disabled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Point expiry is disabled. No points were changed.');
        return self::SUCCESS;
    }
}
