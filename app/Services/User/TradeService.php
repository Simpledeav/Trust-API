<?php

namespace App\Services\User;

use App\Models\Asset;
use App\Models\Trade;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class TradeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $asset = Asset::findOrFail($data['asset_id']);
            $balance = $user->wallet->getBalance($data['wallet']);
            $amount = ($asset->price * $data['quantity']);

            if ($balance < $amount) {
                abort(403, 'Insufficient balance in your wallet account.');
            }

            $trade = Trade::create([
                'user_id'     => $user->id,
                'asset_id'    => $data['asset_id'],
                'asset_type'  => $data['asset_type'],
                'type'        => $data['type'],
                'price'       => $asset->price,
                'quantity'    => $data['quantity'],
                'amount'      => $amount,
                'status'      => $data['status'] ?? 'open',
                'entry'       => $data['entry'] ?? null,
                'exit'        => $data['exit'] ?? null,
                'leverage'    => $data['leverage'] ?? null,
                'interval'    => $data['interval'] ?? null,
                'tp'          => $data['tp'] ?? null,
                'sl'          => $data['sl'] ?? null,
                'extra'       => 0,
            ]);

            $user->wallet->debit($amount, $data['wallet'], 'Trade create');
            $user->storeTransaction($amount, $trade->id, 'App/Models/Trade', 'debit', 'approved', 'Order created on ' . $asset->symbol . ' of ' . $data['quantity'] . ' units', null, null, now());

            return $trade;
        });
    }

    public function update(Trade $trade, array $data)
    {
        return DB::transaction(function () use ($trade, $data) {
            $trade->update($data);
            return $trade;
        });
    }

    public function toggleStatus(Trade $trade, $user, $request)
    {
        // Ensure the trade belongs to the authenticated user
        if ($user->id !== $trade->user_id) {
            abort(403, 'Unauthorized: This trade does not belong to you.');
        }

        // Prevent reopening a closed trade
        if ($trade->status === 'close') {
            abort(400, 'You cannot toggle a closed trade.');
        }

        if ($trade->status === $request->status) {
            abort(400, 'Trade is already ' . $trade->status);
        }

        if ($request->status === 'close') {
            $asset = Asset::findOrFail($trade['asset_id']);
            $amount = ($asset->price * $trade['quantity']);

            if($amount > 0) {
                $user->wallet->credit($amount, 'wallet', 'Trade close');
                $user->storeTransaction($amount, $trade->id, 'App/Models/Trade', 'credit', 'approved', 'Open Order', null, null, now());
            }
        }

        // Update the trade status
        $trade->update(['status' => $request->status]);

        return $trade;
    }

    public function createPosition(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $wallet = $data['wallet'];

            $asset = Asset::findOrFail($data['asset_id']);
            $balance = $user->wallet->getBalance($wallet);
            $newAmount = $asset->price * $data['quantity'];

            // Check if user already has an open position for this asset
            $existingPosition = Position::where('user_id', $user->id)
                ->where('asset_id', $data['asset_id'])
                ->where('status', 'open')
                ->lockForUpdate() // Prevents race conditions
                ->first();

            if ($existingPosition) {
                // If position exists, update quantity and amount
                $newQuantity = $existingPosition->quantity + $data['quantity'];
                $updatedAmount = $existingPosition->amount + $newAmount;

                if ($balance < $newAmount) {
                    abort(403, 'Insufficient balance to add more to this position.');
                }

                if ($newAmount < 1) {
                    abort(403, 'Cannot open position, amount is less than 1.');
                }

                $existingPosition->update([
                    'quantity' => $newQuantity,
                    'amount' => $updatedAmount,
                ]);

                $user->wallet->debit($newAmount, $wallet, 'Added to an existing position');
                $user->storeTransaction($newAmount, $existingPosition->id, Position::class, 'debit', 'approved', "Added {$data['quantity']} units to {$asset->symbol} position", null, null, now());

                return $existingPosition;
            } else {
                // Create a new position if none exists
                if ($balance < $newAmount) {
                    abort(403, 'Insufficient balance to open a new position.');
                }

                $trade = Position::create([
                    'user_id'    => $user->id,
                    'asset_id'   => $data['asset_id'],
                    'asset_type' => $asset->type,
                    'account'        => $data['wallet'],
                    'price'      => $asset->price,
                    'quantity'   => $data['quantity'],
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

                // Store trades as position transaction history
                Trade::create([
                    'user_id'     => $user->id,
                    'asset_id'    => $data['asset_id'],
                    'asset_type'  => $asset->type,
                    'type'        => 'buy',
                    'account'        => $data['wallet'],
                    'price'       => $asset->price,
                    'quantity'    => $data['quantity'],
                    'amount'      => $newAmount,
                    'status'      => $data['status'] ?? 'open',
                    'entry'       => $data['entry'] ?? null,
                    'exit'        => $data['exit'] ?? null,
                    'leverage'    => $data['leverage'] ?? null,
                    'interval'    => $data['interval'] ?? null,
                    'tp'          => $data['tp'] ?? null,
                    'sl'          => $data['sl'] ?? null,
                    'extra'       => 0,
                ]);

                // $user->wallet->debit($newAmount, $wallet, 'Opened a new position');
                // $user->storeTransaction($newAmount, $trade->id, Position::class, 'debit', 'approved', "Opened a new position on {$asset->symbol} with {$data['quantity']} units", null, null, now());

                return $trade;
            }
        });
    }

    public function closePosition(Position $position, $user, $request)
    {
        return DB::transaction(function () use ($position, $user, $request) {
            // Validate asset existence
            $asset = Asset::findOrFail($position->asset_id);
            
            // Ensure the position belongs to the authenticated user
            if ($user->id !== $position->user_id) {
                abort(403, 'Unauthorized: This trade does not belong to you.');
            }

            // Prevent reopening a closed or locked trade
            if ($position->status === 'locked') {
                abort(400, 'You cannot close this locked position.');
            }

            if ($position->quantity <= 0) {
                abort(400, 'Empty position: Contact admin for more information.');
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

            // Handle wallet transactions
            if ($amount !== 0) {
                $transactionType = $amount > 0 ? 'credit' : 'debit';
                $adjustedAmount = abs($amount);
                $user->wallet->{$transactionType}($adjustedAmount, 'wallet', $comment);
                if($adjustedAmount > 0.00)
                    $user->storeTransaction($adjustedAmount, $position->id, Position::class, $transactionType, 'approved', $comment, null, null, now());
            }

            // Store trades as position transaction history
            Trade::create([
                'user_id'     => $user->id,
                'asset_id'    => $position['asset_id'],
                'asset_type'  => $asset->type,
                'type'        => 'sell',
                'price'       => $asset->price,
                'quantity'    => $request['quantity'],
                'account'    => 'wallet',
                'amount'      => $closingValue,
                'status'      => $position['status'] ?? 'open',
                'entry'       => $position['entry'] ?? null,
                'exit'        => $position['exit'] ?? null,
                'leverage'    => $position['leverage'] ?? null,
                'interval'    => $position['interval'] ?? null,
                'tp'          => $position['tp'] ?? null,
                'sl'          => $position['sl'] ?? null,
                'extra'       => 0,
                'pl'          => $pl,
                'pl_percentage'=> $plPercentage,
            ]);

            // Close entire position
            if ($position->quantity === $request['quantity']) {
                $position->delete();
                return 'Order closed successfully';
            }

            // Close part of the position
            if ($position->quantity > $request['quantity']) {
                $newQuantity = $position->quantity - $request['quantity'];
                $newAmount = $position->price * $newQuantity;
                $position->update(['quantity' => $newQuantity, 'amount' => $newAmount]);
                return $position;
            }

            // Requested quantity exceeds available position
            abort(400, 'Invalid quantity: You cannot close more than your available position.');
        });
    }

}
