<?php

namespace App\Http\Controllers\Admin;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\SavingsAccount;
use App\Http\Controllers\Controller;
use App\Models\Savings;

class SavingsController extends Controller
{
    public function index()
    {
        $savingsSummary = Savings::selectRaw('
            user_id,
            SUM(balance) as balance,
            COUNT(id) as accounts
        ')
            ->with('user') // Load related user data
            ->groupBy('user_id')
            ->paginate(20);

        return view('admin.savings', [
            'savings' => $savingsSummary
        ]);
    }

    public function accounts()
    {
        $accounts = SavingsAccount::paginate(20);

        return view('admin.accounts', [
            'accounts' => $accounts
        ]);
    }
}
