<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\NotificationController;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        $query = Transaction::query();

        // Check if the user is filtering by deposit or withdrawal
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if($request->type == 'credit')
            $type = "Depoist";
        elseif($request->type == 'debit')
            $type = "Withdrawal";
        elseif($request->type == 'transfer')
            $type = "Transfer";
        else
            $type = "Transactions";

        $users = User::all();
        $transactions = $query->latest()->paginate(20);

        return view('admin.transaction', [
            'transactions' => $transactions,
            'title' => $type,
            'users' => $users,
        ]);
    }

    public function addTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'amount' => ['required'],
            'account' => ['required', 'in:wallet,cash,brokerage,auto,ira'],
            'type' => ['required'],
            'comment' => ['sometimes'],
            'created_at' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::where('id', $request['user_id'])->first();
        
        if(!$user) {
            return back()->with('error', 'User does not exist!');
        }

        if($request->type == 'credit')
        {
            $amount = $request->amount;
            $comment = $request->comment ?? "Credit by Admin";

            $user->wallet->credit($amount, $request->account, $comment);

            $transaction = $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'credit', 'approved', $comment, null, null, Carbon::parse($request->created_at)->format('Y-m-d H:i:s'));

            if($transaction)
                return redirect()->back()->with('success', 'Account credited successfully');
            
        } elseif($request->type == 'debit') {

            $amount = $request->amount;
            $comment = $request->comment ?? "Debited by Admin";

            $balance = $user->wallet->getBalance($request->account);
            if($amount > $balance)
                return back()->with('error', 'Insufficient Account balance');

            $user->wallet->debit($amount, $request->account, $comment);

            $transaction = $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'debit', 'approved', $comment, null, null, Carbon::parse($request->created_at)->format('Y-m-d H:i:s'));

            if($transaction)
                return redirect()->back()->with('success', 'Account debited successfully');
        } elseif($request->type == 'transfer') {


        }
        
        return redirect()->back()->with('error', 'Something went worng!! ');
    }

    public function destroyTransaction(Transaction $transaction)
    {   
        $amount = $transaction->amount;

        $user = User::where('id', $transaction['user_id'])->first();
        
        if(!$user) {
            return back()->with('error', 'User does not exist!');
        }

        if($transaction->type == 'credit') {

            $balance = $user->wallet->getBalance('wallet');
            if($amount > $balance)
                return back()->with('error', 'Transaction cannot be deleted, due to unavailable funds');

            $user->wallet->debit($amount, 'wallet', 'Revesed transaction');

            $balance = $transaction->balance - $amount;

        } elseif($transaction->type == 'debit') {

            $user->wallet->credit($amount, 'wallet', 'Revesed transaction');

        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }


    public function deposit(Request $request, Transaction $transaction)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approved,decline',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = $transaction->user;

        // APPROVE DEPOSIT 
        if($request->action == 'approved') {
            
            $user->wallet->credit($transaction->amount, 'wallet', 'Admin approved deposit');

            $transaction->update(['status' => 'approved']);
            
        } elseif($request->action == 'decline') {

            $transaction->update(['status' => 'declined',]);

        } else {
            return back()->with('error', 'Error process transaction, try again');
        }

        // NotificationController::sendWelcomeEmailNotification($user);

        return back()->with('success', 'Transaction updates successfully');
    }

    public function withdraw(Request $request, Transaction $transaction)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approved,decline',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = $transaction->user;

        if($request->action == 'approved') {
            
            $user->wallet->debit($transaction->amount, 'wallet', 'Admin approved withdrawal');

            $transaction->update(['status' => 'approved']);
            
        } elseif($request->action == 'decline') {

            $transaction->update(['status' => 'declined',]);

        } else {
            return back()->with('error', 'Error process transaction, try again');
        }

        // NotificationController::sendWelcomeEmailNotification($user);

        return back()->with('success', 'Transaction updates successfully');
    }

    // Decline a transaction
    public function decline(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->status = 'declined';
        $transaction->save();

        return redirect()->back()->with('success', 'Transaction declined successfully.');
    }
}
