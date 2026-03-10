<?php

namespace Core\Users\Commands;

use Carbon\Carbon;
use Core\Users\Models\Contract;
use Core\Users\Models\Point;
use Core\Users\Models\User;
use Illuminate\Console\Command;

class HandleExpiredContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-expired-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle expired contract';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contracts = Contract::query()
        ->whereDate('end_date',Carbon::now())
        ->get();
        foreach ($contracts as $contract) {
            User::where('operator_id',$contract->client_id)->update([
                'contract_note' => null,
                'contract_expiration_date' => null,
                'operator_id' => null
            ]);
            User::whereDate('contract_expiration_date',Carbon::now())->update([
                'contract_note' => null,
                'contract_expiration_date' => null,
                'operator_id' => null
            ]);
        }
        }
    }
