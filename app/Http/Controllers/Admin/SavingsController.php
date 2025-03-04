<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Account;
use App\Models\Savings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SavingsLedger;
use App\Models\SavingsAccount;
use Illuminate\Support\Carbon;
use PhpParser\Node\Stmt\Return_;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;

class SavingsController extends Controller
{
    public function index()
    {
        $savings = Savings::latest()->paginate(20);
        $accounts = SavingsAccount::all();
        $users = User::all();

        return view('admin.savings', [
            'savings' => $savings,
            'users' => $users,
            'accounts' => $accounts,
        ]);
    }

    public function accounts()
    {
        $accounts = SavingsAccount::paginate(20);
        $countries = Country::all();

        return view('admin.accounts', [
            'accounts' => $accounts,
            'countries' => $countries,
        ]);
    }

    public function fetchTransactions()
    {
        $transactions = SavingsLedger::latest()->paginate(20);

        return view('admin.savings-profit', [
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required'],
            'user_id' => ['required'],
            'created_at' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::where('id', $request['user_id'])->first();
        
        if(!$user) {
            return back()->with('error', 'User does not exist!');
        }
        
        // Prevent duplicate savings account type for the user
        if ($user->savings()->where('savings_account_id', $request->account_id)->exists()) {
            return redirect()->back()->with('error', 'User already have this savings account.');
        }

        $accounts = Savings::create([
            'user_id' => $request['user_id'],
            'savings_account_id' => $request['account_id'],
            'balance' => 0,
            'old_balance' => 0,
            'comment' => 'Admin created account',
            'created_at' => Carbon::parse($request['created_at'])->format('Y-m-d H:i:s'),
        ]);

        if($accounts)
            return redirect()->back()->with('success', 'Account created successfully.');

        return redirect()->back()->with('error', 'Error Storing Account');
    }

    public function storeAccounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'title' => ['required'],
            'note' => ['required'],
            'countries_id' => ['required', 'array'], // Ensure it is an array
            'countries_id.*' => ['exists:countries,id'], // Validate each country ID
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $accounts = SavingsAccount::create([
            'name' => $request['name'],
            'slug' => Str::slug($request['name']),
            'title' => $request['title'],
            'note' => $request['note'],
            'country_id' => json_encode($request->countries_id),
            'status' => 'active',
        ]);

        if($accounts)
            return redirect()->back()->with('success', 'Account created successfully.');

        return redirect()->back()->with('error', 'Error Storing Account');
    }

    public function updateAccounts(Request $request, SavingsAccount $savingsAccount)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'title' => ['required'],
            'note' => ['required'],
            'countries_id' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $savingsAccount->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'title' => $request->title,
            'note' => $request->note,
            'country_id' => json_encode($request->countries_id), // Store as JSON array
        ]);

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    public function destroyAccount(SavingsAccount $savingsAccount)
    {   
        // Check if there are any related Savings records
        if ($savingsAccount->savings()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete account: It is linked to existing savings.');
        }
    
        // Proceed with deletion if no related savings exist
        $savingsAccount->delete();

        return redirect()->back()->with('success', 'Account deleted successfully.');
    }

    public function transactions(User $user, Savings $savings)
    {
        $transactions = SavingsLedger::where('user_id', $user->id)
                                    ->where('savings_id', $savings->id)
                                    ->latest()
                                    ->paginate(20);

        return view('admin.savings-transactions', [
            'transactions' => $transactions,
            'user' => $user,
            'savings' => $savings,
        ]);
    }

    public function contribute(Request $request, User $user, Savings $savings)
    {
        $amount = $request->amount;
        $type = $request->type;
        $created_at  = Carbon::parse($request->created_at)->format('Y-m-d H:i:s');

        switch ($type) {
            case 'credit':
                $balance = $user->wallet->getBalance('wallet');
                if ($balance < $amount) {
                    return back()->with('error', 'Insufficient funds in users wallet balance.');
                }

                $user->wallet->debit($amount, 'wallet', 'Savings Contribution to ' . $savings->savingsAccount->name . ' account.');

                SavingsLedger::record($user, 'credit', $savings->id, $amount, 'contribution', 'Admin Contribution', $created_at);

                // Update balances
                $savings->update([
                    'old_balance' => $savings->balance,
                    'balance' => $savings->balance + $amount
                ]);

                break;
            case 'debit':
                $balance = $savings->balance;
                if ($balance < $amount) {
                    return back()->with('error', "Insufficient funds in your " . $savings->savingsAccount->name . " account.");
                }

                $user->wallet->credit($amount, 'wallet', 'Savings Cashout from ' . $savings->savingsAccount->name . ' account.');

                SavingsLedger::record($user, 'debit', $savings->id, $amount, 'contribution', 'Admin Contribution', $created_at);

                // Update balances
                $savings->update([
                    'old_balance' => $savings->balance,
                    'balance' => $savings->balance - $amount
                ]);

                break;
            case 'profit':
                SavingsLedger::record($user, 'credit', $savings->id, $amount, 'profit', 'Admin Contribution', $created_at);

                // Update balances
                $savings->update([
                    'old_balance' => $savings->balance,
                    'balance' => $savings->balance + $amount
                ]);

                break;
            default:
                return back()->with('error', 'Wrong method');
        }

        

        if($savings)
            return back()->with('success', 'Contribution added successfully');

        return back()->with('error', 'Error making contributions');
    }

    public function destroy(SavingsLedger $savingsLedger)
    {   
        $amount = $savingsLedger->amount;

        $savings = Savings::findOrFail($savingsLedger['savings_id']);

        if($savingsLedger->type == 'credit') {
            $balance = $savings->balance - $amount;
        } else {
            $balance = $savings->balance + $amount;
        }

        $savings->update([
            'old_balance' => $savings->balance,
            'balance' => $balance,
        ]);

        $savingsLedger->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }

}
