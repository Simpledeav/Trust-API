<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Asset;
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required'],
            'user_id' => ['required'],
            'account' => ['required', 'in:wallet,brokerage,auto'],
            'quantity' => ['required'],
            'amount' => ['sometimes'],
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

        $wallet = $request->account;
        $balance = $user->wallet->getBalance($wallet);

            $newAmount = $asset->price * $request->quantity;

            // Check if user already has an open position for this asset
            $existingPosition = Position::where('user_id', $user->id)
                ->where('asset_id', $request->asset_id)
                ->where('status', 'open')
                ->lockForUpdate() // Prevents race conditions
                ->first();

            if ($existingPosition) {
                // If position exists, update quantity and amount
                $newQuantity = $existingPosition->quantity + $request->quantity;
                $updatedAmount = $existingPosition->amount + $newAmount;

                if ($balance < $newAmount) {
                    return back()->with('error', 'Insufficient balance to add more to this position.');
                }

                if ($newAmount < 1) {
                    return back()->with('error', 'Cannot open position, amount is less than 1.');
                }

                $existingPosition->update([
                    'quantity' => $newQuantity,
                    'amount' => $updatedAmount,
                ]);

                $user->wallet->debit($newAmount, $wallet, 'Added to an existing position');
                $user->storeTransaction($newAmount, $existingPosition->id, Position::class, 'debit', 'approved', "Added {$request['quantity']} units to {$asset->symbol} position", null, null, now());

                return back()->with('success', "Added {$request['quantity']} units to {$asset->symbol} position");
            } else {
                // Create a new position if none exists
                if ($balance < $newAmount) {
                    return back()->with('error', 'Insufficient balance to open a new position.');
                }

                $trade = Position::create([
                    'user_id'    => $user->id,
                    'asset_id'   => $request['asset_id'],
                    'asset_type' => $asset->type,
                    'price'      => $asset->price,
                    'quantity'   => $request->quantity,
                    'amount'     => $newAmount,
                    'status'     => $data['status'] ?? 'open',
                    'entry'      => $data['entry'] ?? null,
                    'exit'       => $data['exit'] ?? null,
                    'leverage'   => $data['leverage'] ?? null,
                    'interval'   => $data['interval'] ?? null,
                    'tp'         => $data['tp'] ?? null,
                    'sl'         => $data['sl'] ?? null,
                    'extra'      => 0,
                ]);

                $user->wallet->debit($newAmount, $wallet, 'Opened a new position');
                $user->storeTransaction($newAmount, $trade->id, Position::class, 'debit', 'approved', "Opened a new position on {$asset->symbol} with {$request['quantity']} units", null, null, now());

                return back()->with('success', 'Postion created successfully');
            }

            return back()->withInput()->with('error', 'Error processing trade');
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

        if ($position->quantity === $request['quantity']) {
            // If closing entire position, delete it
            if ($amount > 0) {
                $user->wallet->credit($amount, 'wallet', $comment);
                $user->storeTransaction($amount, $position->id, Position::class, 'credit', 'approved', $comment, null, null, now());
            }

            $position->delete();

            return back()->with('success', 'Order closed successfully');
        }

        if ($position->quantity > $request['quantity']) {
            // If closing part of the position, update it
            $newQuantity = $position->quantity - $request['quantity'];
            $newAmount = ($position->price * $newQuantity);

            if ($amount > 0) {
                $user->wallet->credit($amount, 'wallet', $comment);
                $user->storeTransaction($amount, $position->id, Position::class, 'credit', 'approved', $comment, null, null, now());
            }

            $position->update([
                'quantity' => $newQuantity,
                'amount'   => $newAmount
            ]);

            return back()->with('success', 'Order closed successfully');
        }

        return redirect()->back()->with('error', 'Invalid quantity: You cannot close more than your available position.');
    }
}
