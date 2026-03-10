<?php

namespace Core\Users\Commands;

use Core\Users\Models\Point;
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
    protected $description = 'Handle expired points';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $points = Point::whereDate('expire_at', now())
        ->has('user')
        ->where('operation', 'deposit')
        ->get();
        foreach ($points as $point) {
            $usedAmountSinceCreation = Point::where('user_id', $point->user_id)
            ->where('id', '!=', $point->id)
            ->where('created_at', '>=', $point->created_at)
            ->where('operation', 'withdraw')
            ->sum('amount');
            if($usedAmountSinceCreation < $point->amount){
                $notUsedAmount = $point->amount - $usedAmountSinceCreation;
                Point::create([
                   'title'      => 'Expired Points',
                   'amount'     => $notUsedAmount,
                   'operation'  => 'withdraw',
                   'expire_at'  => null,
                   'user_id'    => $point->user_id,
                ]);
            }
        }
    }
}
