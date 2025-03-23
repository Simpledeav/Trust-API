<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Asset;
use App\Models\Trade;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index()
    {
        $trade = Position::latest()->paginate(10);

        $users = User::all();

        $assets = Asset::all();

        return view('admin.position', [
            'trades' => $trade,
            'users' => $users,
            'assets' => $assets,
        ]);
    }

    public function fetch()
    {
        $trade = Trade::latest()->paginate(10);

        $users = User::all();

        $assets = Asset::all();

        return view('admin.position-history', [
            'trades' => $trade,
            'users' => $users,
            'assets' => $assets,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required'],
            'user_id' => ['required'],
            'account' => ['required', 'in:wallet,brokerage,auto'],
            'quantity' => ['required', 'numeric', 'min:0.000001'],
            'amount' => ['sometimes', 'numeric', 'min:0.1'],
            'entry' => ['sometimes'],
            'exit' => ['sometimes'],
            'tp' => ['sometimes'],
            'sl' => ['sometimes'],
            'leverage' => ['sometimes'],
            'extra' => ['required'],
            'created_at' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $user = User::find($request['user_id']);
        if (!$user) {
            return back()->with('error', 'User does not exist!');
        }

        $asset = Asset::find($request['asset_id']);
        if (!$asset) {
            return back()->with('error', 'Asset not found!');
        }

        $wallet = $request->account;
        $balance = $user->wallet->getBalance($wallet);
        $newAmount = $asset->price * $request->quantity;

        if ($balance < $newAmount) {
            return back()->with('error', 'Insufficient balance.');
        }

        $existingPosition = Position::where('user_id', $user->id)
            ->where('asset_id', $request->asset_id)
            ->where('status', 'open')
            ->lockForUpdate()
            ->first();

        if ($existingPosition) {
            $newQuantity = $existingPosition->quantity + $request->quantity;
            $updatedAmount = $existingPosition->amount + $newAmount;

            $existingPosition->update([
                'quantity' => $newQuantity,
                'amount' => $updatedAmount,
            ]);

            // Store trades as position transaction history
            Trade::create([
                'user_id'     => $user->id,
                'asset_id'    => $request['asset_id'],
                'asset_type'  => $asset->type,
                'account'     => $wallet,
                'type'        => 'buy',
                'price'       => $asset->price,
                'quantity'    => $request['quantity'],
                'amount'      => $newAmount,
                'status'      => 'open',
                'entry'       => $request['entry'] ?? null,
                'exit'        => $request['exit'] ?? null,
                'leverage'    => $request['leverage'] ?? null,
                'interval'    => $request['interval'] ?? null,
                'tp'          => $request['tp'] ?? null,
                'sl'          => $request['sl'] ?? null,
                'extra'       => 0,
            ]);

            $user->wallet->debit($newAmount, $wallet, 'Added to an existing position');

            return back()->with('success', "Added {$request['quantity']} units to {$asset->symbol} position");
        } else {
            Position::create([
                'user_id'    => $user->id,
                'asset_id'   => $request['asset_id'],
                'asset_type' => $asset->type,
                'account'    => $wallet,
                'price'      => $asset->price,
                'quantity'   => $request->quantity,
                'amount'     => $newAmount,
                'status'     => 'open',
                'entry'      => $request['entry'] ?? null,
                'tp'         => $request['tp'] ?? null,
                'sl'         => $request['sl'] ?? null,
                'leverage'   => $request['leverage'] ?? null,
                'extra'      => $request['extra'],
                'created_at' => $request['created_at'],
            ]);

            // Store trades as position transaction history
            Trade::create([
                'user_id'     => $user->id,
                'asset_id'    => $request['asset_id'],
                'asset_type'  => $asset->type,
                'account'    => $wallet,
                'type'        => 'buy',
                'price'       => $asset->price,
                'quantity'    => $request['quantity'],
                'amount'      => $newAmount,
                'status'      => $request['status'] ?? 'open',
                'entry'       => $request['entry'] ?? null,
                'exit'        => $request['exit'] ?? null,
                'leverage'    => $request['leverage'] ?? null,
                'interval'    => $request['interval'] ?? null,
                'tp'          => $request['tp'] ?? null,
                'sl'          => $request['sl'] ?? null,
                'extra'       => 0,
            ]);

            $user->wallet->debit($newAmount, $wallet, 'Opened a new position');

            return back()->with('success', 'Position created successfully');
        }

        return back()->with('error', 'Error processing trade');
    }

    public function close(Request $request)
    {
        $user = User::where('id', $request['user_id'])->first();
        
        if(!$user) {
            return back()->with('error', 'Username does not exist!');
        }

        // Find the asset safely
        $asset = Asset::find($request->asset_id);

        // Find the position safely
        $position = Position::find($request->position_id);

        if (!$asset) {
            return back()->with('error', 'Asset not found. Please contact support.');
        }

        if (!$position) {
            return back()->with('error', 'Position not found. Please contact support.');
        }

        $amount = ($asset->price * $request['quantity']);
        $comment = "Closed position on " . $asset->name . " of " . $amount;

        if ($position->quantity <= 0) {
            return back()->with('error', 'Null position: Contact admin for more information.');
        }

        // Lock position row to prevent race conditions
        $position->lockForUpdate();

        // Calculate amounts
        $newPrice = $asset->price * $request['quantity'];
        $amount = $newPrice - ($position->price * $request['quantity']) + $position['extra'];
        $comment = "Closed position on {$asset->name} of {$amount}";

        // Calculate amounts
        $closingValue = $asset->price * $request['quantity']; // Current price × quantity
        $openingValue = $position->price * $request['quantity']; // Position price × quantity
        $pl = $closingValue - $openingValue + $position['extra']; // Profit/Loss
        $plPercentage = ($pl / $openingValue) * 100; // Profit/Loss Percentage

        $wallet = $position->account ?? 'wallet';
        $user->wallet->credit($position->price * $request['quantity'], $wallet, $comment);

        // Handle wallet transactions
        if ($amount !== 0) {
            $transactionType = $amount > 0 ? 'credit' : 'debit';
            $adjustedAmount = abs($amount);
            $user->wallet->{$transactionType}($adjustedAmount, $wallet, $comment);
            if($adjustedAmount > 0)
                $user->storeTransaction($adjustedAmount, $position->id, Position::class, $transactionType, 'approved', $comment, null, null, now());
        }

        // Store trades as position transaction history
        Trade::create([
            'user_id'     => $user->id,
            'asset_id'    => $position['asset_id'],
            'asset_type'  => $asset->type,
            'account'    => 'wallet',
            'type'        => 'sell',
            'price'       => $asset->price,
            'account'     => 'wallet',
            'quantity'    => $position['quantity'],
            'amount'      => $newPrice,
            'status'      => 'open',
            'entry'       => $position['entry'] ?? null,
            'exit'        => $position['exit'] ?? null,
            'leverage'    => $position['leverage'] ?? null,
            'interval'    => $position['interval'] ?? null,
            'tp'          => $position['tp'] ?? null,
            'sl'          => $position['sl'] ?? null,
            'pl'          => $pl,
            'pl_percentage'=> $plPercentage,
            'extra'       => 0,
        ]);

        // Close entire position
        if ($position->quantity === $request['quantity']) {

            // Check if there are any remaining positions for the same asset and user
            $remainingPositions = Position::where('user_id', $user->id)
                ->where('asset_id', $position->asset_id)
                ->where('quantity', '>', $request['quantity'])
                ->exists();

            // If no remaining positions, update all related trades to "closed"
            if (!$remainingPositions) {
                Trade::where('user_id', $user->id)
                    ->where('asset_id', $position->asset_id)
                    ->update(['status' => 'close']);
            }

            $position->delete();

            return back()->with('success', 'Order closed successfully');
        }

        // Close part of the position
        if ($position->quantity > $request['quantity']) {
            $newQuantity = $position->quantity - $request['quantity'];
            $newAmount = $position->price * $newQuantity;
            $position->update(['quantity' => $newQuantity, 'amount' => $newAmount]);
            return back()->with('success', 'Order closed successfully');
        }
        
        return redirect()->back()->with('error', 'Invalid quantity: You cannot close more than your available position.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['sometimes', 'required'],
            'user_id' => ['sometimes', 'required'],
            'account' => ['sometimes', 'required', 'in:wallet,brokerage,auto'],
            'quantity' => ['sometimes', 'required', 'numeric', 'min:0.000001'],
            'amount' => ['sometimes', 'numeric', 'min:0.1'],
            'entry' => ['sometimes'],
            'exit' => ['sometimes'],
            'tp' => ['sometimes'],
            'sl' => ['sometimes'],
            'leverage' => ['sometimes'],
            'interval' => ['sometimes'],
            'extra' => ['sometimes', 'required'],
            'created_at' => ['sometimes', 'required', 'date'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        // Find the position to update
        $position = Position::find($id);
        if (!$position) {
            return back()->with('error', 'Position not found!');
        }

        // Find the user
        $user = User::find($request->input('user_id', $position->user_id));
        if (!$user) {
            return back()->with('error', 'User does not exist!');
        }

        // Find the asset
        $asset = Asset::find($request->input('asset_id', $position->asset_id));
        if (!$asset) {
            return back()->with('error', 'Asset not found!');
        }

        // Check if the account balance is sufficient for the updated position
        $wallet = $request->input('account', $position->account);
        $balance = $user->wallet->getBalance($wallet);

        $newAmount = $asset->price * $request->input('quantity', $position->quantity);

        if ($balance < $newAmount) {
            return back()->with('error', 'Insufficient balance.');
        }

        // Update the position
        $position->update([
            'asset_id'   => $request->input('asset_id', $position->asset_id),
            'user_id'    => $request->input('user_id', $position->user_id),
            'account'    => $wallet,
            'price'      => $asset->price,
            'quantity'   => $request->input('quantity', $position->quantity),
            'amount'     => $newAmount,
            'status'     => $request->input('status', $position->status),
            'entry'      => $request->input('entry', $position->entry),
            'exit'      => $request->input('exit', $position->exit),
            'interval'      => $request->input('interval', $position->interval),
            'tp'         => $request->input('tp', $position->tp),
            'sl'         => $request->input('sl', $position->sl),
            'leverage'   => $request->input('leverage', $position->leverage),
            'extra'      => $request->input('extra', $position->extra),
            'created_at' => $request->input('created_at', $position->created_at),
        ]);

        // Update or create a trade record for the position transaction history
        // Trade::updateOrCreate(
        //     [
        //         'user_id'  => $position->user_id,
        //         'asset_id' => $position->asset_id,
        //         'type'     => 'buy', // Assuming this is a buy trade
        //     ],
        //     [
        //         'asset_type' => $asset->type,
        //         'account'   => $wallet,
        //         'price'      => $asset->price,
        //         'quantity'   => $request->input('quantity', $position->quantity),
        //         'amount'     => $newAmount,
        //         'status'     => $request->input('status', $position->status),
        //         'entry'      => $request->input('entry', $position->entry),
        //         'exit'       => $request->input('exit', $position->exit),
        //         'leverage'   => $request->input('leverage', $position->leverage),
        //         'interval'   => $request->input('interval', $position->interval),
        //         'tp'        => $request->input('tp', $position->tp),
        //         'sl'        => $request->input('sl', $position->sl),
        //         'extra'     => 0, // Assuming extra is not part of the trade history
        //     ]
        // );

        return back()->with('success', 'Position updated successfully');
    }
}
