<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\State;
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
        $balance = $user->wallet->getBalance('wallet');
        $brokerage_balance = $user->wallet->getBalance('brokerage');
        $auto_balance = $user->wallet->getBalance('auto');
        $savings_balance = $user->savings()->sum('balance');
        $currencies = Currency::all();

        $countries = Country::where('status', 'active')->orderBy('name', 'ASC')->get();
        $states = State::all();

        $transactions = $user->transactionsFetch()->paginate(10);
        $savings_account = $user->savings()->paginate(10);
        $trades = $user->trade()->paginate(10);

        $deposit = $user->depositAccount()->first();
        $withdrawal = $user->withdrawalAccount()->first();

        return view('admin.user-details', [
            'user' => $user,
            'balance' => $balance,
            'brokerage_balance' => $brokerage_balance,
            'auto_balance' => $auto_balance,
            'savings_balance' => $savings_balance,
            'currencies' => $currencies,
            'transactions' => $transactions,
            'savings_account' => $savings_account,
            'trades' => $trades,
            'deposit' => $deposit,
            'withdrawal' => $withdrawal,
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'employed' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
        ]);

        // Update the user data
        $user->update([
            'first_name' => $validated['first_name'] ?? $user->first_name,
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'email' => $validated['email'] ?? $user->email,
            'address' => $validated['address'] ?? $user->address,
            'country' => $validated['country'] ?? $user->country,
            'state' => $validated['state'] ?? $user->state,
            'zipcode' => $validated['zipcode'] ?? $user->zipcode,
            'dob' => $validated['dob'] ?? $user->dob,
            'employed' => $validated['employed'] ?? $user->employed,
            'nationality' => $validated['nationality'] ?? $user->nationality,
            'experience' => $validated['experience'] ?? $user->experience,
            'currency_id' => $validated['currency_id'] ?? $user->currency_id,
            'country_id' => $validated['country_id'] ?? $user->country_id,
            'state_id' => $validated['state_id'] ?? $user->state_id,
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

    public function kyc(Request $request, User $user)
    {
        $validated = $request->validate([
            'action' => 'in:approved,declined',
        ]);

        // Update the user data
        $data = $user->update([
            'kyc' => $validated['action'],
        ]);
        
        if($data)
            // Redirect back with success message
            return redirect()->back()->with('success', 'User kyc updated successfully.');

        return redirect()->back()->with('error', 'User action failed!');
        
    }

    public function trades()
    {
        $trade = Trade::latest()->paginate(10);

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

        $transaction = $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'credit', 'approved', 'Admin credited user', null, null, now());

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

        $transaction = $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'debit', 'approved', 'Admin debited user', null, null, now());

        if($transaction)
            return redirect()->back()->with('success', 'Account debited successfully');
        
        return redirect()->back()->with('error', 'Something went worng!!');
    }

    public function bank(Request $request, User $user)
    {
        // Validate the request
        $validated = $request->validate([
            // 'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:user,admin'],
            'btc_wallet' => ['nullable', 'string'],
            'eth_wallet' => ['nullable', 'string'],
            'trc_wallet' => ['nullable', 'string'],
            'erc_wallet' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'account_name' => ['nullable', 'string'],
            'bank_number' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'bank_routing_number' => ['nullable', 'string'],
            'bank_reference' => ['nullable', 'string'],
            'bank_address' => ['nullable', 'string'],
        ]);

        // Find the user's payment data based on type
        $payment = $user->payments()->where('type', $validated['type'])->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment data not found for this user.');
        }

        // Update the payment details
        $updated = $payment->update($validated);

        if ($updated) {
            return redirect()->back()->with('success', 'User payment details updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update payment details.');
    }

    public function destroy(User $user)
    {
        // Ensure all related models are loaded before deletion
        $user->load([
            'wallet', 
            'transactions', 
            'transactionsFetch',
            'savings', 
            'trade', 
            'payments'
        ]);

        // Delete related data manually if cascading is not set in DB
        $user->wallet()->delete();
        $user->transactions()->delete();
        $user->transactionsFetch()->delete();
        $user->savings()->delete();
        $user->trade()->delete();
        $user->payments()->delete();

        // Finally, delete the user
        $user->forceDelete();

        return redirect()->back()->with('success', 'User and all related data deleted successfully.');
    }

}
