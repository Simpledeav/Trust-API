<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\DataTransferObjects\Models\TransactionModelData;

class TransactionService
{
    
    public function create(TransactionModelData $data, User $user): Transaction
    {
        return Transaction::query()->create($data->toArray())->refresh();
    }

    public function swap(TransactionModelData $data, User $user): Transaction
    {
        // Validate that the user has sufficient balance before swapping
        if ($user->wallet->getBalance($data->getSwapFrom()) < $data->getAmount()) {
            throw new \Exception("Insufficient balance in {$data->getSwapFrom()}.");
        }

        // Create a transaction record
        $transaction = $user->storeTransaction(
            $data->getAmount(),
            $user->wallet->id,
            'App/Models/Wallet',
            'transfer',
            'approved',
            $data->getComment(),
            $data->getSwapFrom(),
            $data->getSwapTo(),
            Carbon::parse(now())->format('Y-m-d H:i:s')
        );

        // Perform the transfer by debiting and crediting the respective wallets
        $user->wallet->debit($data->getAmount(), $data->getSwapFrom(), "Transfer to {$data->getSwapTo()}");
        $user->wallet->credit($data->getAmount(), $data->getSwapTo(), "Received from {$data->getSwapFrom()}");

        return $transaction;
    }

    public function cancel(Transaction $transaction, User $user): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status' => 'cancelled',
            ]);
    
            return $transaction->fresh(); // Return updated instance
        });
    }
}
