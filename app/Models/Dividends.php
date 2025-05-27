<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Dividends extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'amount',
        'percent_value',
        'account',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];
}
