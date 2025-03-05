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
            $balance = $user->wallet->getBalance('wallet');
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

            $user->wallet->debit($amount, 'wallet', 'Trade create');
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

                $user->wallet->debit($newAmount, $wallet, 'Opened a new position');
                $user->storeTransaction($newAmount, $trade->id, Position::class, 'debit', 'approved', "Opened a new position on {$asset->symbol} with {$data['quantity']} units", null, null, now());

                return $trade;
            }
        });
    }

    public function closePosition(Position $position, $user, $request)
    {
        return DB::transaction(function () use ($position, $user, $request) {
            logger($request);
            // Find the asset safely
            $asset = Asset::find($position->asset_id);

            if (!$asset) {
                return abort(403, 'Asset not found. Please contact support.');
            }

            $amount = ($asset->price * $request['quantity']);
            $comment = "Closed position on " . $asset->name . " of " . $amount;

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

            if ($position->quantity === $request['quantity']) {
                // If closing entire position, delete it
                if ($amount > 0) {
                    $user->wallet->credit($amount, 'wallet', $comment);
                    $user->storeTransaction($amount, $position->id, Position::class, 'credit', 'approved', $comment, null, null, now());
                }

                $position->delete();

                return 'Order closed successfully';
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

                return $position;
            }

            // If requested quantity is more than available, return error
            abort(400, 'Invalid quantity: You cannot close more than your available position.');
        });
    }

}
