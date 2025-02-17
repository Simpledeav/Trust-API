<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset' => ['required'],
            'username' => ['required'],
            'type' => ['required', 'in:buy,sell'],
            'quantity' => ['required'],
            'amount' => ['required'],
            'entry' => ['sometimes'],
            'tp' => ['sometimes'],
            'sl' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::where('username', $request['username'])->first();
        
        if(!$user) {
            return back()->with('error', 'Username does not exist!');
        }

        // $asset = Asset::where('id', $request['asset'])->first();
        $asset = Asset::where('symbol', $request['asset'])->first();
        
        if(!$asset) {
            return back()->with('error', 'Asset not found!');
        }

        $amount = $request->amount;

        $balance = $user->wallet->getBalance('wallet');

        if($amount < $balance)
            $user->wallet->debit($amount, 'wallet', 'Admin deposit');
        else
            return back()->with('error', 'Insufficient Wallet balance');

        $comment = 'Trade Order on ' . $asset->name;
        
        $trade = $user->placeTrade([
            'asset_id' => $asset->id,
            'asset_type' => $asset->type == 'stocks' ? 'stock' : $asset->type,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'status' => 'open',
            'entry' => $request->entry,
            'tp' => $request->tp,
            'sl' => $request->sl,
        ]);

        $user->wallet->debit($amount, 'wallet', $comment);

        $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'debit', 'approved', $comment);

        if($trade)
            return back()->with('success', 'Order created successfully');

        return back()->withInput()->with('error', 'Error processing trade');
    }

    public function update(Request $request, Trade $trade)
    {
        $validator = Validator::make($request->all(), [
            'asset' => ['required'],
            'username' => ['required'],
            'type' => ['required', 'in:buy,sell'],
            'quantity' => ['required'],
            'amount' => ['required'],
            'entry' => ['sometimes'],
            'tp' => ['sometimes'],
            'sl' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::where('username', $request['username'])->first();
        
        if(!$user) {
            return back()->with('error', 'Username does not exist!');
        }

        $asset = Asset::where('id', $request['asset'])->first();
        
        if(!$asset) {
            return back()->with('error', 'Asset not found!');
        }

        $balance = $user->balance('wallet');

        $amount = $request->amount;

        if($amount > $balance) {
            return back()->with('error', 'Insufficient Balance');
        }

        $trade->update([
            'asset_id' => $asset->id,
            'asset_type' => $asset->type == 'stocks' ? 'stock' : $asset->type,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'status' => 'open',
            'entry' => $request->entry,
            'tp' => $request->tp,
            'sl' => $request->sl,
        ]);

        if($trade)
            return back()->with('success', 'Successfully updated');

        return back()->withInput()->with('error', 'Error processing trade');
    }

    public function toggle(Request $request, Trade $trade)
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:open,close,hold'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        if($request->action == 'open')
        {

        }

        if($request->action == 'close')
        {
            
        }

        $trade->update([
            'status' => $request->action,
        ]);

        if($trade)
            return back()->with('success', 'Successfully updated');

        return back()->withInput()->with('error', 'Error processing trade');
    }
}
