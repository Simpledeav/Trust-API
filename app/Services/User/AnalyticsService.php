<?php

namespace App\Services\User;

use Carbon\Carbon;
use App\Models\Trade;
use App\Models\Ledger;
use App\Models\Savings;
use App\Models\Position;
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
        // $rawTotalInvestment24hr = $positions->sum(function($trade) {
        //     $currentValue = $trade->quantity * $trade->asset->price;
        //     return $currentValue - $trade->amount + $trade->extra;
        // });

        //:::: Incase it comes to todays PL for all possitions
        $rawTotalInvestment24hr = $positions->sum(function($trade) {
            $currentValue = ($trade->asset->change * $trade->quantity)+ $trade->extra;
            return $currentValue ;
        });

        // Get chart data
        $chartData = $this->getChartData(clone $ledgerQuery, $timeframe);

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

    private function getChartData($ledgerQuery, string $timeframe): array
    {
        $groupByFormat = match ($timeframe) {
            '1d' => '%Y-%m-%d %H:00:00',  // Hourly for last 24 hours
            '7d', '30d' => '%Y-%m-%d 00:00:00',  // Daily for last 7 or 30 days
            '1yr', 'all' => '%Y-%m-01 00:00:00',  // Monthly for 1yr or all-time
            default => '%Y-%m-%d 00:00:00',
        };

        $ledgerData = $ledgerQuery
            ->selectRaw("DATE_FORMAT(created_at, ?) as period, SUM(amount) as total_amount", [$groupByFormat])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return $this->fillMissingDates($ledgerData, $timeframe);
    }

    private function fillMissingDates($ledgerData, string $timeframe): array
    {
        $filledData = [];
        $now = Carbon::now();
        $start = match ($timeframe) {
            '1d' => $now->copy()->subHours(23)->startOfHour(),
            '7d' => $now->copy()->subDays(6)->startOfDay(),
            '30d' => $now->copy()->subDays(29)->startOfDay(),
            '1yr' => $now->copy()->subMonths(11)->startOfMonth(),
            'all' => optional($ledgerData->first())->period ? Carbon::parse($ledgerData->first()->period) : $now,
            default => $now,
        };

        $interval = match ($timeframe) {
            '1d' => '1 hour',
            '7d', '30d' => '1 day',
            '1yr', 'all' => '1 month',
            default => '1 day',
        };

        $period = $start->copy();
        $existingData = $ledgerData->pluck('total_amount', 'period')->toArray();

        while ($period->lte($now)) {
            $formattedDate = match ($timeframe) {
                '1d' => $period->format('Y-m-d H:00:00'),
                '7d', '30d' => $period->format('Y-m-d 00:00:00'),
                '1yr', 'all' => $period->format('Y-m-01 00:00:00'),
                default => $period->format('Y-m-d 00:00:00'),
            };

            $filledData[$formattedDate] = isset($existingData[$formattedDate]) ? (float)$existingData[$formattedDate] : 0;
            $period->modify("+{$interval}");
        }

        return $filledData;
    }

}
