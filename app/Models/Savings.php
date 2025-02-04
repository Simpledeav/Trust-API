<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Savings extends Model
{
    use HasFactory;
    use UUID;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savingsAccount()
    {
        return $this->belongsTo(SavingsAccount::class);
    }
}
