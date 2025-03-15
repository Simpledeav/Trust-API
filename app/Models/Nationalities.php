<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Nationalities extends Model
{
    use HasFactory;
    use UUID;
    
    protected $guarded = [];
}
