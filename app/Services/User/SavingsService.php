<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Savings;
use App\Models\SavingsLedger;
use App\Models\SavingsAccount;
use Illuminate\Support\Facades\DB;
use App\Repositories\SavingsRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SavingsService
{
    protected $savingsRepository;

    public function __construct(SavingsRepository $savingsRepository)
    {
        $this->savingsRepository = $savingsRepository;
    }

    public function create(User $user, SavingsAccount $savingsAccount)
    {
        return $this->savingsRepository->assignSavingsAccountToUser($user, $savingsAccount);
    }

    public function credit($user, $savingsAccount, float $amount, string $method, ?string $comment = null)
    {
        return DB::transaction(function () use ($user, $savingsAccount, $amount, $method, $comment) {
            // Ensure savings record exists for the user
            $savings = Savings::where('user_id', $user->id)
                            ->where('savings_account_id', $savingsAccount)
                            ->firstOrFail();

            $wallet_balance = $user->wallet->getBalance('wallet');

            if ($wallet_balance < $amount) {
                throw new \Exception("Insufficient funds in your wallet balance.");
            }

            $user->wallet->debit($amount, 'wallet', 'Savings Contribution to ' . $savings->savingsAccount->name . ' savings account.');

            // Update balances
            $savings->update([
                'old_balance' => $savings->balance,
                'balance' => $savings->balance + $amount
            ]);

            // Record transaction in ledger
            SavingsLedger::record($user, 'credit', $savings->id, $amount, $method, $comment, now());

            return $this->getBalance($user, $savings);
        });
    }

    public function debit($user, $savingsAccount, float $amount, string $method, ?string $comment = null)
    {
        return DB::transaction(function () use ($user, $savingsAccount, $amount, $method, $comment) {
            $savings = Savings::where('user_id', $user->id)
                ->where('savings_account_id', $savingsAccount)
                ->firstOrFail();

            if ($savings->balance < $amount) {
                throw new \Exception("Insufficient funds in your " . $savings->savingsAccount->name . " savings account.");
            }

            $savings->update([
                'old_balance' => $savings->balance,
                'balance' => $savings->balance - $amount
            ]);

            SavingsLedger::record($user, 'debit', $savings->id, $amount, $method, $comment, now());

            $user->wallet->credit($amount, 'wallet', 'Savings Cashout to wallet balance.');

            return $this->getBalance($user, $savings);
        });
    }

    public function getBalance($user, $savingsAccount)
    {
        $totalCredit = SavingsLedger::where('user_id', $user->id)
            ->where('savings_id', $savingsAccount->id)
            ->where('type', 'credit')
            ->sum('amount');

        $totalDebit = SavingsLedger::where('user_id', $user->id)
            ->where('savings_id', $savingsAccount->id)
            ->where('type', 'debit')
            ->sum('amount');

        return $totalCredit - $totalDebit;
    }
}
