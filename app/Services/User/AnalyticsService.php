<?php

namespace App\Services\User;

use Carbon\Carbon;
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

        // Base query with user and approved status
        $ledgerQuery = Transaction::where('status', 'approved')
            ->where('user_id', $user->id);

        // Apply timeframe filter
        if ($timeFilters[$timeframe]) {
            $ledgerQuery->where('created_at', '>=', $timeFilters[$timeframe]);
        }

        $transactionQuery = Transaction::where('status', 'approved')
            ->where('user_id', $user->id)
            ->where('transactable_id', $user->wallet->id);

        $totalDeposited = (clone $transactionQuery)->where('type', 'credit')->sum('amount');
        $totalWithdrawn = (clone $transactionQuery)->where('type', 'debit')->sum('amount');

        // Savings Query
        $savingsQuery = SavingsLedger::where('user_id', $user->id);

        // Total Savings: Sum of contributions
        $creditSavings = (clone $savingsQuery)->where('type', 'credit')->where('method', 'contribution')->sum('amount');
        $debitSavings = (clone $savingsQuery)->where('type', 'debit')->where('method', 'contribution')->sum('amount');
        $totalSavings = number_format(($creditSavings - $debitSavings), 2);

        // Total Return: (credit - contribution + profit)
        $creditTotalSavings = (clone $savingsQuery)->where('type', 'credit')->sum('amount');
        $debitTotalSavings = (clone $savingsQuery)->where('type', 'debit')->sum('amount');
        $totalReturn = number_format(($creditTotalSavings - $debitTotalSavings), 2);

        // 24hr Amount Change: Compare current savings with savings 24 hours ago
        $savingsLast24h = (clone $savingsQuery)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('amount');
        
        $savings24hrChange = number_format(($savingsLast24h), 2);

        // Get chart data in required format
        $chartData = $this->getChartData(clone $ledgerQuery, $timeframe);

        // Get Trades data
        $trades = Position::where('user_id', $user->id);
        $openTrade = (clone $trades)->where('status', 'open')->sum('amount');
        $total_investment = number_format(($totalSavings + $openTrade), 2);

        //Get 24hr total_investments
        $tradeLast24h = (clone $trades)->where('created_at', '>=', now()->subHours(24))->sum('amount');
        $total_investment_24hr = number_format(($savings24hrChange + $tradeLast24h), 2);

        return [
            'total_deposited' => number_format($totalDeposited, 2),
            'total_withdrawn' => number_format($totalWithdrawn, 2),
            'total_savings' => $totalSavings,
            'total_savings_return' => $totalReturn,
            'total_savings_24hr' => $savings24hrChange,
            'total_investment' => $total_investment,
            'total_investment_24hr' => $total_investment_24hr,
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
