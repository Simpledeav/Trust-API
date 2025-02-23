<?php

namespace App\Services\User;

use App\Models\Asset;
use App\Models\Trade;
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


}
