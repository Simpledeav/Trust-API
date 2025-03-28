<?php

namespace App\Services\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Trade;
use App\Models\Ledger;
use App\Models\Savings;
use App\Models\Position;
use Carbon\CarbonPeriod;
use App\Models\Transaction;
use App\Models\SavingsLedger;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get analytics data for the user.
     */

    public function getUserAnalytics($user, string $timeframe = 'all'): array
    {
        // Allowed timeframes
        $timeFilters = [
            '1d' => now()->subHours(24),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '1yr' => now()->subYear(),
            'all' => null, // No filter for "all"
        ];

        // Validate timeframe
        if (!array_key_exists($timeframe, $timeFilters)) {
            throw new \InvalidArgumentException('Invalid timeframe. Allowed values: 1d, 7d, 30d, 1yr, all.');
        }

        // Base queries
        $transactionQuery = Transaction::where('status', 'approved')
            ->where('user_id', $user->id)
            ->where('transactable_id', $user->wallet->id);

        $savingsQuery = SavingsLedger::where('user_id', $user->id);
        $tradesQuery = Trade::where('user_id', $user->id);
        $positionQuery = Position::where('user_id', $user->id);

        // Apply timeframe filter to ledger query if needed
        $ledgerQuery = Transaction::where('status', 'approved')
            ->where('user_id', $user->id);
        
        if ($timeFilters[$timeframe]) {
            $ledgerQuery->where('created_at', '>=', $timeFilters[$timeframe]);
        }

        // Calculate raw values first (without formatting)
        $totalDeposited = (clone $transactionQuery)->where('type', 'credit')->sum('amount');
        $totalWithdrawn = (clone $transactionQuery)->where('type', 'debit')->sum('amount');

        // Savings calculations
        $creditSavings = (clone $savingsQuery)->where('type', 'credit')->where('method', 'contribution')->sum('amount');
        $debitSavings = (clone $savingsQuery)->where('type', 'debit')->where('method', 'contribution')->sum('amount');
        $rawTotalSavings = $creditSavings - $debitSavings;

        $creditTotalSavings = (clone $savingsQuery)->where('type', 'credit')->where('method', 'profit')->sum('amount');
        $debitTotalSavings = (clone $savingsQuery)->where('type', 'debit')->where('method', 'profit')->sum('amount');
        $rawTotalReturn = $creditTotalSavings - $debitTotalSavings;

        $savingsLast24h = (clone $savingsQuery)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('amount');

        // Get all positions with their assets
        $positions = (clone $positionQuery)
            ->with('asset')
            ->get();

        // Trades calculations
        $rawTotalBuy = (clone $tradesQuery)
            ->where('type', 'buy')
            ->where('status', 'open')
            ->sum('amount');

        $rawTotalPl = (clone $tradesQuery)
            ->where('type', 'sell')
            ->sum('pl');

        $totalExtra = $positions
            ->sum('extra');

        // Calculate total extra from all positions
        $totalExtra = $positions
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('extra');

        // Calculate sum of all positions
        $rawTotalInvestment = $positions->sum(function($trade) {
            $currentValue = $trade->quantity * $trade->asset->price;
            return $currentValue;
        }) + $positions->sum('extra');

        // $rawTotalInvestment = $rawTotalBuy + $rawTotalPl + $totalExtra;

        // Get trades from last 24 hours with their assets
        // $tradesLast24h = (clone $tradesQuery)
        //     ->where('status', 'open')
        //     ->where('type', 'buy')
        //     ->where('created_at', '>=', now()->subHours(24))
        //     ->with('asset')
        //     ->get();

        // Calculate 24-hour P&L: (current value - invested amount) + extra from positions
        $rawTotalInvestment24hr = $positions->sum(function($trade) {
            $currentValue = $trade->quantity * $trade->asset->price;
            return $currentValue - $trade->amount + $trade->extra;
        });

        //:::: Incase it comes to todays PL for all possitions
        // $rawTotalInvestment24hr = $positions->sum(function($trade) {
        //     $currentValue = ($trade->asset->change * $trade->quantity)+ $trade->extra;
        //     return $currentValue ;
        // });

        // Get chart data
        // $chartData = $this->getChartData(clone $ledgerQuery, $timeframe);
        // $chartData = $this->getNetWorthChartData($user, $timeFilters[$timeframe]);

        // $timeframe = '1d'; // default to 1 day
        $chartData = $this->getNetworthChartData($user, $timeframe);

        return [
            'total_deposited' => number_format($totalDeposited, 2),
            'total_withdrawn' => number_format($totalWithdrawn, 2),
            'total_savings' => number_format($rawTotalSavings, 2),
            'total_savings_return' => number_format($rawTotalReturn, 2),
            'total_savings_24hr' => number_format($savingsLast24h, 2),
            'total_investment' => number_format($rawTotalInvestment, 2),
            'total_investment_24hr' => number_format($rawTotalInvestment24hr, 2),
            'chart_data' => $chartData,
        ];
    }

    /**
     * Get networth chart data for a user based on timeframe
     * 
     * @param \App\Models\User $user
     * @param string $timeframe (1d, 7d, 30d, 1yr, all)
     * @return array
     */
    public function getNetworthChartData(User $user, string $timeframe): array
    {
        $now = now();
        $chartData = [];
        
        switch ($timeframe) {
            case '1d':
                // Last 24 hours, hourly data
                for ($i = 23; $i >= 0; $i--) {
                    $time = $now->copy()->subHours($i)->startOfHour();
                    $endTime = $time->copy()->endOfHour();
                    
                    $networth = $this->calculateNetworthAtTime($user, $endTime);
                    
                    $chartData[$time->format('Y-m-d H:i:s')] = $networth;
                }
                break;
                
            case '7d':
                // Last 7 days, daily data
                for ($i = 6; $i >= 0; $i--) {
                    $time = $now->copy()->subDays($i)->startOfDay();
                    $endTime = $time->copy()->endOfDay();
                    
                    $networth = $this->calculateNetworthAtTime($user, $endTime);
                    
                    $chartData[$time->format('Y-m-d 00:00:00')] = $networth;
                }
                break;
                
            case '30d':
                // Last 30 days, daily data
                for ($i = 29; $i >= 0; $i--) {
                    $time = $now->copy()->subDays($i)->startOfDay();
                    $endTime = $time->copy()->endOfDay();
                    
                    $networth = $this->calculateNetworthAtTime($user, $endTime);
                    
                    $chartData[$time->format('Y-m-d 00:00:00')] = $networth;
                }
                break;
                
            case '1yr':
                // Last 12 months, monthly data
                for ($i = 11; $i >= 0; $i--) {
                    $time = $now->copy()->subMonths($i)->startOfMonth();
                    $endTime = $time->copy()->endOfMonth();
                    
                    $networth = $this->calculateNetworthAtTime($user, $endTime);
                    
                    $chartData[$time->format('Y-m-01 00:00:00')] = $networth;
                }
                break;
                
            case 'all':
                // All available data (from first transaction to now)
                $firstTransactionDate = $this->getFirstTransactionDate($user);
                
                if ($firstTransactionDate) {
                    $currentDate = $firstTransactionDate->copy()->startOfDay();
                    
                    while ($currentDate <= $now) {
                        $endTime = $currentDate->copy()->endOfDay();
                        $networth = $this->calculateNetworthAtTime($user, $endTime);
                        
                        $chartData[$currentDate->format('Y-m-d 00:00:00')] = $networth;
                        
                        $currentDate->addDay();
                    }
                }
                break;
        }
        
        return $chartData;
    }

    /**
     * Calculate networth at a specific point in time
     * 
     * @param \App\Models\User $user
     * @param \Carbon\Carbon $time
     * @return float
     */
    protected function calculateNetworthAtTime(User $user, Carbon $time): float
    {
        // 1. Calculate cash balance (wallet balance at that time)
        // $cashBalance = Transaction::where('user_id', $user->id)
        //     ->where('status', 'approved')
        //     ->where('created_at', '<=', $time)
        //     ->where('type', 'credit')
        //     ->sum('amount');

        // 1. Calculate cash balance (wallet balance at that time)
        $creditBalance = Transaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('type', 'credit')
            ->where('created_at', '<=', $time)
            ->sum('amount');

        $debitBalance = Transaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('type', 'debit')
            ->where('created_at', '<=', $time)
            ->sum('amount');

        $cashBalance = $creditBalance - $debitBalance;
        
        // 2. Calculate total investment (positions value at that time)
        $totalInvestment = Position::where('user_id', $user->id)
            ->where('status', 'open')
            ->where('created_at', '<=', $time)
            ->with(['asset' => function($query) use ($time) {
                $query->select('id', 'price')
                    ->where('updated_at', '<=', $time)
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            }])
            ->get()
            ->sum(function($position) {
                return ($position->quantity * ($position->asset->price ?? 0)) + $position->extra - $position->amount;
            });
        
        // 3. Calculate total savings (savings ledger up to that time)
        $creditSavings = SavingsLedger::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('created_at', '<=', $time)
            ->sum('amount');
        
        $debitSavings = SavingsLedger::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('created_at', '<=', $time)
            ->sum('amount');
        
        $totalSavings = $creditSavings - $debitSavings;
        $total_networth = $cashBalance + $totalInvestment + $totalSavings;
        
        return number_format($total_networth, 2);
    }

    /**
     * Get the date of the first transaction for a user
     * 
     * @param \App\Models\User $user
     * @return \Carbon\Carbon|null
     */
    protected function getFirstTransactionDate(User $user): ?Carbon
    {
        $firstTransaction = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->first();
        
        return $firstTransaction ? $firstTransaction->created_at : null;
    }

    //:::::: FRESH METHODS
    // protected function getNetWorthChartData($user, $startDate = null): array
    // {
    //     $now = now();
        
    //     // 1. Get all data up to now if no start date (for 'all' timeframe)
    //     $cashQuery = Transaction::where('user_id', $user->id)
    //         ->where('swap_from', 'wallet')
    //         ->where('status', 'approved');
        
    //     $investmentQuery = Position::where('user_id', $user->id)
    //         ->join('assets', 'assets.id', '=', 'positions.asset_id');
        
    //     $savingsQuery = SavingsLedger::where('user_id', $user->id);
        
    //     // Apply timeframe filter if provided
    //     if ($startDate) {
    //         $cashQuery->where('created_at', '>=', $startDate);
    //         $investmentQuery->where('positions.created_at', '>=', $startDate);
    //         $savingsQuery->where('created_at', '>=', $startDate);
    //     }
        
    //     // 2. Group data by appropriate time intervals
    //     $groupByFormat = $this->getGroupByFormat($startDate);
        
    //     $cashData = $cashQuery
    //         ->selectRaw("DATE_FORMAT(created_at, ?) as date, SUM(amount) as cash", [$groupByFormat])
    //         ->groupBy('date')
    //         ->get()
    //         ->keyBy('date')
    //         ->map(function ($item) {
    //             return $item->cash;
    //         })
    //         ->toArray();
        
    //     $investmentData = $investmentQuery
    //         ->selectRaw("DATE_FORMAT(positions.created_at, ?) as date, 
    //             SUM(positions.quantity * assets.price) + SUM(positions.extra) as investments", 
    //             [$groupByFormat])
    //         ->groupBy('date')
    //         ->get()
    //         ->keyBy('date')
    //         ->map(function ($item) {
    //             return $item->investments;
    //         })
    //         ->toArray();
        
    //     $savingsData = $savingsQuery
    //         ->selectRaw("DATE_FORMAT(created_at, ?) as date, SUM(amount) as savings", [$groupByFormat])
    //         ->groupBy('date')
    //         ->get()
    //         ->keyBy('date')
    //         ->map(function ($item) {
    //             return $item->savings;
    //         })
    //         ->toArray();
        
    //     // 3. Combine all dates and calculate net worth
    //     $allDates = array_unique(array_merge(
    //         array_keys($cashData),
    //         array_keys($investmentData),
    //         array_keys($savingsData)
    //     ));
    //     sort($allDates);
        
    //     $chartData = [];
    //     foreach ($allDates as $date) {
    //         $chartData[$date] = 
    //             ($cashData[$date] ?? 0) + 
    //             ($investmentData[$date] ?? 0) + 
    //             ($savingsData[$date] ?? 0);
    //     }
        
    //     // 4. Fill any missing dates with previous value
    //     return $this->fillMissingNetWorthDates($chartData, $startDate);
    // }

    // protected function getGroupByFormat($startDate = null): string
    // {
    //     if ($startDate === null) {
    //         return '%Y-%m-01 00:00:00'; // Monthly for all time
    //     }

    //     // Get the actual difference in days
    //     $days = now()->diffInDays($startDate);
        
    //     // For specific timeframes, use these groupings regardless of actual days
    //     if ($days <= 1) {
    //         return '%Y-%m-%d %H:00:00';  // Hourly for last 24 hours
    //     } elseif ($days <= 7) {
    //         return '%Y-%m-%d 00:00:00';  // Daily for last 7 days
    //     } elseif ($days <= 30) {
    //         return '%Y-%m-%d 00:00:00';  // Daily for last 30 days
    //     } else {
    //         return '%Y-%m-01 00:00:00';  // Monthly for longer periods
    //     }
    // }

    // protected function fillMissingNetWorthDates(array $netWorthData, $startDate = null): array
    // {
    //     $filledData = [];
    //     $now = now();
        
    //     // Determine interval based on timeframe
    //     if ($startDate === null) {
    //         $interval = '1 month';
    //         $format = 'Y-m-01 00:00:00';
    //         $start = !empty($netWorthData) 
    //             ? Carbon::createFromFormat('Y-m-d H:i:s', array_key_first($netWorthData))
    //             : $now->subYear()->startOfMonth();
    //     } else {
    //         $days = $now->diffInDays($startDate);
    //         if ($days <= 1) {
    //             $interval = '1 hour';
    //             $format = 'Y-m-d H:00:00';
    //             $start = $now->copy()->subHours(23)->startOfHour();
    //         } elseif ($days <= 7) {
    //             $interval = '1 day';
    //             $format = 'Y-m-d 00:00:00';
    //             $start = $now->copy()->subDays(6)->startOfDay();
    //         } elseif ($days <= 30) {
    //             $interval = '1 day';
    //             $format = 'Y-m-d 00:00:00';
    //             $start = $now->copy()->subDays(29)->startOfDay();
    //         } else {
    //             $interval = '1 month';
    //             $format = 'Y-m-01 00:00:00';
    //             $start = $now->copy()->subMonths(11)->startOfMonth();
    //         }
    //     }

    //     $current = $start->copy();
    //     $lastValue = 0;

    //     while ($current <= $now) {
    //         $period = $current->format($format);
    //         $value = $netWorthData[$period] ?? $lastValue;
    //         $filledData[$period] = $value;
    //         $lastValue = $value;
    //         $current->modify("+{$interval}");
    //     }

    //     return $filledData;
    // }
    //:::::: FRESH METHODS
    
    // private function getChartData($ledgerQuery, string $timeframe): array
    // {
    //     $groupByFormat = match ($timeframe) {
    //         '1d' => '%Y-%m-%d %H:00:00',  // Hourly for last 24 hours
    //         '7d', '30d' => '%Y-%m-%d 00:00:00',  // Daily for last 7 or 30 days
    //         '1yr', 'all' => '%Y-%m-01 00:00:00',  // Monthly for 1yr or all-time
    //         default => '%Y-%m-%d 00:00:00',
    //     };

    //     $ledgerData = $ledgerQuery
    //         ->selectRaw("DATE_FORMAT(created_at, ?) as period, SUM(amount) as total_amount", [$groupByFormat])
    //         ->groupBy('period')
    //         ->orderBy('period')
    //         ->get();

    //     return $this->fillMissingDates($ledgerData, $timeframe);
    // }

    // private function fillMissingDates($ledgerData, string $timeframe): array
    // {
    //     $filledData = [];
    //     $now = Carbon::now();
    //     $start = match ($timeframe) {
    //         '1d' => $now->copy()->subHours(23)->startOfHour(),
    //         '7d' => $now->copy()->subDays(6)->startOfDay(),
    //         '30d' => $now->copy()->subDays(29)->startOfDay(),
    //         '1yr' => $now->copy()->subMonths(11)->startOfMonth(),
    //         'all' => optional($ledgerData->first())->period ? Carbon::parse($ledgerData->first()->period) : $now,
    //         default => $now,
    //     };

    //     $interval = match ($timeframe) {
    //         '1d' => '1 hour',
    //         '7d', '30d' => '1 day',
    //         '1yr', 'all' => '1 month',
    //         default => '1 day',
    //     };

    //     $period = $start->copy();
    //     $existingData = $ledgerData->pluck('total_amount', 'period')->toArray();

    //     while ($period->lte($now)) {
    //         $formattedDate = match ($timeframe) {
    //             '1d' => $period->format('Y-m-d H:00:00'),
    //             '7d', '30d' => $period->format('Y-m-d 00:00:00'),
    //             '1yr', 'all' => $period->format('Y-m-01 00:00:00'),
    //             default => $period->format('Y-m-d 00:00:00'),
    //         };

    //         $filledData[$formattedDate] = isset($existingData[$formattedDate]) ? (float)$existingData[$formattedDate] : 0;
    //         $period->modify("+{$interval}");
    //     }

    //     return $filledData;
    // }

}
