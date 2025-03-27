<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;
    use UUID;

    protected $guarded = [];

    // In your Asset model
    public function scopeMaxPrice($query, $price)
    {
        return $query->where('price', '<=', $price);
    }

    public function scopeMinPrice($query, $price)
    {
        return $query->where('price', '>=', $price);
    }

    public function scopeMarketCapMin($query, $value)
    {
        return $query->where('market_cap', '>=', $value);
    }

    public function scopeMarketCapMax($query, $value)
    {
        return $query->where('market_cap', '<=', $value);
    }

    public function scopeVolumeMin($query, $value)
    {
        return $query->where('volume', '>=', $value);
    }

    public function scopeVolumeMax($query, $value)
    {
        return $query->where('volume', '<=', $value);
    }
}
