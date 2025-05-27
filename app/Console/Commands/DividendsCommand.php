<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Position;
use App\Models\Dividends;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DividendsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dividends:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily dividends for all active positions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all users with active positions that have dividends
        $users = User::whereHas('positions', function($query) {
                $query->where('status', 'open')
                      ->where('dividends', '>', 0);
            })
            ->with(['positions' => function($query) {
                $query->where('status', 'open')
                      ->where('dividends', '>', 0)
                      ->with('asset');
            }, 'wallet'])
            ->cursor();
    
        foreach ($users as $user) {
            DB::transaction(function () use ($user) {
                $totalDividendAmount = 0;
                $accountTypes = [];
                
                // Calculate total dividend for all positions
                foreach ($user->positions as $position) {
                    // Calculate current profit/loss with leverage and extra
                    $currentPrice = $position->asset->price;
                    $quantity = $position->quantity;
                    $leverage = abs($position->leverage ?? 1);
                    $extra = $position->extra;
                    
                    // Calculate profit/loss
                    $singleProfit = ($currentPrice * $quantity) - $position->amount;
                    $profit = ($singleProfit * $leverage) + $extra;
                    
                    // Calculate dividend amount for this position
                    $dividendAmount = ($profit * $position->dividends) / 100;
                    $dividendAmount = round($dividendAmount, 2);
                    
                    // Track account types used (for validation)
                    $accountTypes[$position->account] = true;
                    
                    // Add to total dividend
                    $totalDividendAmount += $dividendAmount;

                    $this->info('Loading ' . $user->first_name . ' position of profit: $' . $profit . '($' . $dividendAmount . ')');
                }
                
                // Skip if no dividend to process
                if ($totalDividendAmount == 0) {
                    return;
                }

                if($user->settings->drip == true) {
                    $account = array_key_first($accountTypes);
                } else {
                    $account = 'wallet';
                }
                
                // Create dividend record
                Dividends::create([
                    'user_id' => $user->id,
                    'amount' => $totalDividendAmount,
                    'percent_value' => 0, // Aggregate percentage would be meaningless
                    'account' => $account,
                ]);
                
                // Credit/debit user's wallet
                if ($totalDividendAmount > 0) {
                    $user->wallet->credit(
                        $totalDividendAmount, 
                        $account, 
                        "Daily dividend from all positions"
                    );
                } else {
                    $user->wallet->debit(
                        abs($totalDividendAmount), 
                        $account, 
                        "Daily dividend charge from all positions"
                    );
                }
            });
        }

        $this->info('Completed ' . count($users) . ' Positions Successfully!!');
    }
}
