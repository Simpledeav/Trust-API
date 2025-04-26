<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use UUID;

    protected $fillable = [
        'user_id', 
        'type', 
        'label', 
        'currency',
        'wallet_address', 
        'account_name', 
        'account_number', 
        'bank_name',
        'routing_number',
        'bank_reference',
        'bank_address',
        'is_withdrawal'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
