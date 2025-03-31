<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;
    use UUID;
    
    protected $guarded = [];

    public function getQuerySelectables(): array
    {
        $table = $this->getTable();

        return [
            "{$table}.id",
            "{$table}.user_id",
            "{$table}.message",
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
