<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Savings;
use App\Models\SavingsAccount;

class SavingsRepository
{

    public function assignSavingsAccountToUser(User $user, SavingsAccount $savingsAccount)
    {
        // Create a new savings entry for the user
        $savings = new Savings();
        $savings->user_id = $user->id;
        $savings->savings_account_id = $savingsAccount->savings_account_id;
        $savings->balance = 0;
        $savings->old_balance = 0;
        $savings->save();

        return $savings;
    }
}
