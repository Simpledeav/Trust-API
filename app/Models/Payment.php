<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UUID;

class Payment extends Model
{
    use HasFactory;
    use UUID;

    protected $fillable = [
        'wallet_name', 'type', 'wallet_address', 'wallet_note', 
        'bank_name', 'account_name', 'bank_number', 'bank_account_number', 'bank_routing_number', 'bank_reference',
        'bank_address', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
