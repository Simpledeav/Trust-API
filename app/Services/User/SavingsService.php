<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Admin;
use App\Models\Savings;
use App\Models\SavingsLedger;
use App\Models\SavingsAccount;
use Illuminate\Support\Facades\DB;
use App\Repositories\SavingsRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\NotificationController as Notifications;

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
                            ->with('savingsAccount') // Eager load the savingsAccount relationship
                            ->firstOrFail();

            // Check contribution limits
            if ($amount < $savings->savingsAccount->min_contribution) {
                throw new \Exception("Minimum contribution amount is " . $savings->savingsAccount->min_contribution);
            }

            if ($savings->savingsAccount->max_contribution > 0 && $amount > $savings->savingsAccount->max_contribution) {
                throw new \Exception("Maximum contribution amount is " . $savings->savingsAccount->max_contribution);
            }

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
            SavingsLedger::record($user, 'credit', $savings->id, $amount, $method, 'approved', $comment, now());

            // Send notification
            Notifications::sendSavingsCreditNotification($user, $savings->savingsAccount, $amount, $savings->balance);

            $admin = Admin::where('email', config('app.admin_mail'))->first();
            Notifications::sendAdminNewContributionNotification($admin, $user, $savings->savingsAccount->name, $amount);

            return $this->getBalance($user, $savings);
        });
    }

    public function debit($user, $savingsAccount, float $amount, string $method, ?string $comment = null)
    {
        return DB::transaction(function () use ($user, $savingsAccount, $amount, $method, $comment) {
            $savings = Savings::where('user_id', $user->id)
                ->where('savings_account_id', $savingsAccount)
                ->with('savingsAccount') // Eager load the savingsAccount relationship
                ->firstOrFail();

            // Check if account is locked
            if ($savings->status === 'locked') {
                throw new \Exception($savings->locked_account_message);
            }

            // Check cashout limits
            if ($amount < $savings->savingsAccount->min_cashout) {
                throw new \Exception("Minimum cashout amount is " . $savings->savingsAccount->min_cashout);
            }

            if ($savings->savingsAccount->max_cashout > 0 && $amount > $savings->savingsAccount->max_cashout) {
                throw new \Exception("Maximum cashout amount is " . $savings->savingsAccount->max_cashout);
            }

            if ($savings->balance < $amount) {
                throw new \Exception("Insufficient funds in your " . $savings->savingsAccount->name . " savings account.");
            }

            SavingsLedger::record($user, 'debit', $savings->id, $amount, $method, 'pending', $comment, now());

            // Send notification
            Notifications::sendSavingsDebitNotification($user, $savings->savingsAccount, $amount, $savings->balance);

            $admin = Admin::where('email', config('app.admin_mail'))->first();
            Notifications::sendAdminNewCashoutNotification($admin, $user, $savings->savingsAccount->name, $amount);

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
