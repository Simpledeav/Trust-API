<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SavingsLedger extends Model
{
    use HasFactory;
    use UUID;

    protected $fillable = [
        'user_id', 'savings_id', 'amount', 'type', 'method', 
        'balance', 'old_balance', 'comment'
    ];

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    public static function record(User $user, string $type, string $savingsId, float $amount, string $method, ?string $comment = null): void
    {
        try {
            $savings = Savings::where('id', $savingsId)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

                            logger($savings . " -- " . $user);

            self::create([
                'user_id'    => $user->id,
                'savings_id' => $savings->id, // Fixed reference
                'amount'     => $amount,
                'type'       => $type,
                'method'     => $method,
                'balance'    => $savings->balance,
                'old_balance'=> $savings->old_balance,
                'comment'    => $comment,
            ]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Savings account not found.');
        } catch (\Exception $e) {
            throw new \Exception('Failed to record savings transaction.');
        }
    }
}
