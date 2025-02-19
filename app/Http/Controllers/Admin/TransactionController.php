<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    // public function index()
    // {
    //     $transactions = Transaction::paginate(20);

    //     return view('admin.transaction', [
    //         'transactions' => $transactions
    //     ]);
    // }

    // app/Http/Controllers/TransactionController.php

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
        else
            $type = "Transactions";


        $transactions = $query->latest()->paginate(20);

        return view('admin.transaction', [
            'transactions' => $transactions,
            'title' => $type,
        ]);
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
            
            $user->wallet->debit($transaction->amount, 'brokerage', 'Admin approved withdrawal');

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
