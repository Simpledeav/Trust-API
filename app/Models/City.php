<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;
    use UUID;

    protected $guarded = [];

    public function states()
    {
        return $this->belongsTo(State::class);
    }
}
