<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class AutoPlan extends Model
{
    use UUID;

    protected $fillable = [
        'id', 'name', 'min_invest', 'max_invest', 'win_rate', 'duration',
        'milestone', 'aum', 'expected_returns', 'day_returns', 'img', 'status'
    ];
}
