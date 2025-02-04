<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Currency extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;
    use UUID;

    protected $guarded = [];
}
