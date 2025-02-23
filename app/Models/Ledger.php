<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\ExpectationFailedException;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ledger extends Model
{
    use HasFactory;
    use UUID;

    protected $fillable = [
        'wallet_id', 'amount', 'type', 'account', 
        'balance', 'old_balance', 'comment', 'ledgerable_id', 'ledgerable_type'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function ledgerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create a ledger entry for credit or debit.
     */
    public static function record($ledgerable, string $type, float $amount, string $account, ?string $comment = null): void
    {
        $wallet = $ledgerable->wallet ?? $ledgerable;
        $oldBalance = $wallet->balance;
        $newBalance = $type === 'credit' ? $oldBalance + $amount : $oldBalance - $amount;

        self::create([
            'wallet_id' => $wallet->id,
            'ledgerable_id' => $ledgerable->id,
            'ledgerable_type' => get_class($ledgerable),
            'amount' => $amount,
            'type' => $type,
            'account' => $account,
            'balance' => $newBalance,
            'old_balance' => $oldBalance,
            'comment' => $comment,
        ]);
    }

    public static function balance(Wallet $ledgerable, ?string $account = null): float
    {
        $query = $ledgerable->ledgerEntries();

        // Apply account filtering if an account is specified
        if ($account) {
            $query->where('account', $account);
        }

        // Clone the query to prevent it from being modified
        $credits = (clone $query)->where('type', 'credit')->sum('amount') ?? 0;
        $debits = (clone $query)->where('type', 'debit')->sum('amount') ?? 0;

        return $credits - $debits;
    }

}
