<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;
    use UUID;

    protected $fillable = ['id', 'user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ledgerEntries(): MorphMany
    {
        return $this->morphMany(Ledger::class, 'ledgerable');
    }

    public function debits(): MorphMany
    {
        return $this->ledgerEntries()->where('type', 'debit');
    }

    public function credits(): MorphMany
    {
        return $this->ledgerEntries()->where('type', 'credit');
    }

    /**
     * Credit the wallet and create a ledger entry.
     */
    public function credit(float $amount, string $account = 'wallet', ?string $comment = null): void
    {
        DB::transaction(function () use ($amount, $account, $comment) {
            Ledger::record($this, 'credit', $amount, $account, $comment);
            $this->increment('balance', $amount);
        });
    }

    /**
     * Debit the wallet and create a ledger entry.
     */
    public function debit(float $amount, string $account = 'wallet', ?string $comment = null): void
    {
        DB::transaction(function () use ($amount, $account, $comment) {
            if ($this->balance < $amount) {
                throw new \Exception("Insufficient balance");
            }
            Ledger::record($this, 'debit', $amount, $account, $comment);
            $this->decrement('balance', $amount);
        });
    }

    public function getBalance(?string $account = null): float
    {
        return Ledger::balance($this, $account);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactable');
    }

}
