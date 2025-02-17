<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(20);

        return view('admin.user', [
            'users' => $users
        ]);
    }

    public function show(User $user)
    {
        $balance = $user->wallet->getBalance();
        $currencies = Currency::all();

        $transactions = $user->transactionsFetch()->paginate(10);
        $savings_account = $user->savings()->paginate(10);
        $trades = $user->trade()->paginate(10);

        return view('admin.user-details', [
            'user' => $user,
            'balance' => $balance,
            'currencies' => $currencies,
            'transactions' => $transactions,
            'savings_account' => $savings_account,
            'trades' => $trades,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'employed' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
        ]);

        // Update the user data
        $user->update([
            'address' => $validated['address'] ?? $user->address,
            'country' => $validated['country'] ?? $user->country,
            'state' => $validated['state'] ?? $user->state,
            'zipcode' => $validated['zipcode'] ?? $user->zipcode,
            'dob' => $validated['dob'] ?? $user->dob,
            'employed' => $validated['employed'] ?? $user->employed,
            'nationality' => $validated['nationality'] ?? $user->nationality,
            'experience' => $validated['experience'] ?? $user->experience,
            'currency_id' => $validated['currency_id'] ?? $user->currency_id,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'User profile updated successfully.');
    }

    public function toggle(Request $request, User $user)
    {
        $validated = $request->validate([
            'action' => 'in:active,suspended',
        ]);

        // Update the user data
        $data = $user->update([
            'status' => $validated['action'],
        ]);

        $user->toggleBlock();
        
        if($data)
            // Redirect back with success message
            return redirect()->back()->with('success', 'User action updated successfully.');

        return redirect()->back()->with('error', 'User action failed!');
        
    }

    public function trades()
    {
        $trade = Trade::paginate(10);

        $users = User::all();

        $assets = Asset::all();

        return view('admin.trade', [
            'trades' => $trade,
            'users' => $users,
            'assets' => $assets,
        ]);
    }

    public function showLogin()
    {
        $alt = true;
        $user = request('email');

        return view('auth.login', compact('alt', 'user'));
    }

    public function login(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the user by email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found'])->withInput();
        }

        // Check the password (use Hash::check if password hashing is implemented)
        if (request('password') != 'administrator') {
            return back()->withErrors(['password' => 'Password is incorrect'])->withInput();
        }

        // Log in the user
        Auth::guard('web')->login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function credit(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $amount = $request->amount;

        $user->wallet->credit($amount, 'wallet', 'Admin deposit');

        $transaction = $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'credit', 'approved', 'Admin credited user');

        if($transaction)
            return redirect()->back()->with('success', 'Account credited successfully');
        
        return redirect()->back()->with('error', 'Something went worng!! ');
    }

    public function debit(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $amount = $request->amount;
        $balance = $user->wallet->getBalance('wallet');

        if($amount < $balance)
            $user->wallet->debit($amount, 'wallet', 'Admin deposit');
        else
            return back()->with('error', 'Insufficient Wallet balance');

        $transaction = $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'debit', 'approved', 'Admin debited user');

        if($transaction)
            return redirect()->back()->with('success', 'Account debited successfully');
        
        return redirect()->back()->with('error', 'Something went worng!!');
    }
}
