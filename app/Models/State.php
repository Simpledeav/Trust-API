<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;
    use UUID;

    protected $guarded = [];
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
