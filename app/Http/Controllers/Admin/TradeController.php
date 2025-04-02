<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required'],
            'user_id' => ['required'],
            'type' => ['required', 'in:buy,sell'],
            'account' => ['required', 'in:wallet,brokerage,auto'],
            'quantity' => ['sometimes'],
            'amount' => ['required'],
            'entry' => ['sometimes'],
            'tp' => ['sometimes'],
            'sl' => ['sometimes'],
            'leverage' => ['sometimes'],
            'extra' => ['required'],
            'created_at' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::where('id', $request['user_id'])->first();
        
        if(!$user) {
            return back()->with('error', 'Username does not exist!');
        }

        $asset = Asset::where('id', $request['asset_id'])->first();
        if(!$asset) {
            return back()->with('error', 'Asset not found!');
        }

        $amount = (float) $request->amount;
        $account = $request->account;
        $balance = $user->wallet->getBalance($account);
        $assetPrice = (float) $asset->price;
        $quantity = ($amount / $assetPrice);
        $comment = 'Trade Order on ' . $asset->name;

        if($amount > $balance) {
            return back()->with('error', 'Insufficient ' . $account . ' balance');
        }
        
        $trade = $user->placeTrade([
            'asset_id' => $asset->id,
            'asset_type' => $asset->type == 'stocks' ? 'stock' : $asset->type,
            'type' => $request->type,
            'quantity' => $quantity,
            'amount' => $amount,
            'price' => $assetPrice,
            'status' => 'open',
            'entry' => $request->entry,
            'tp' => $request->tp,
            'sl' => $request->sl,
            'leverage' => $request->leverage,
            'extra' => $request->extra,
            'created_at'  => $request->created_at ? Carbon::parse($request->created_at)->format('Y-m-d H:i:s') : now(),
        ]);

        if($trade)
            $user->wallet->debit($amount, $account, $comment);

            $user->storeTransaction($amount, $user->wallet->id, 'App/Models/Wallet', 'debit', 'approved', 'Open Order by admin', null, null, Carbon::parse($request->created_at)->format('Y-m-d H:i:s'));

            return back()->with('success', 'Order created successfully');

        return back()->withInput()->with('error', 'Error processing trade');
    }

    public function update(Request $request, Trade $trade)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required'],
            'user_id' => ['required'],
            'type' => ['required', 'in:buy,sell'],
            'account' => ['required', 'in:wallet,brokerage,auto'],
            'amount' => ['required'],
            'entry' => ['sometimes'],
            'tp' => ['sometimes'],
            'sl' => ['sometimes'],
            'leverage' => ['sometimes'],
            'extra' => ['required'],
            'created_at' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::where('id', $request['user_id'])->first();
        
        if(!$user) {
            return back()->with('error', 'User does not exist!');
        }

        $asset = Asset::where('id', $request['asset_id'])->first();
        
        if(!$asset) {
            return back()->with('error', 'Asset not found!');
        }

        $amount = (float) $request->amount;
        $account = $request->account;
        $balance = $user->wallet->getBalance($account);
        $assetPrice = (float) $asset->price;
        $quantity = ($amount / $assetPrice);
        $comment = 'Trade Order on ' . $asset->name;

        if($amount > $balance) {
            return back()->with('error', 'Insufficient Balance');
        }

        $user->wallet->credit($trade->amount, $account, $comment);
        $user->wallet->debit($amount, $account, $comment);

        $trade->update([
            'asset_id' => $asset->id,
            'asset_type' => $asset->type == 'stocks' ? 'stock' : $asset->type,
            'type' => $request->type,
            'quantity' => $quantity,
            'amount' => $amount,
            'price' => $assetPrice,
            'status' => 'open',
            'entry' => $request->entry,
            'tp' => $request->tp,
            'sl' => $request->sl,
            'leverage' => $request->leverage,
            'extra' => $request->extra,
            'created_at'  => Carbon::parse($request->created_at),
        ]);

        if($trade)
            return back()->with('success', 'Successfully updated');

        return back()->withInput()->with('error', 'Error processing trade');
    }

    public function updateHistory(Request $request, Trade $trade)
    {
        $validator = Validator::make($request->all(), [
            'created_at' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $trade->update([
            'created_at'  => Carbon::parse($request->created_at),
        ]);

        if($trade)
            return back()->with('success', 'Successfully updated');

        return back()->withInput()->with('error', 'Error processing trade');
    }

    public function destroyHistory(Trade $trade)
    {
        $trade->delete();

        return redirect()->back()->with('success', 'Trade history deleted successfully.');
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

    public function destroy(Trade $trade)
    {
        $trade->delete();

        return redirect()->back()->with('success', 'Trade Order deleted successfully.');
    }
}
