<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class SavingsAccount extends Model
{
    use HasFactory;
    use UUID;

    protected $guarded = [];

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }
}