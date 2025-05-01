<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use UUID;

    protected $fillable = [
        'id',
        'user_id',
        'amount',
        'transactable_id',
        'transactable_type',
        'type',
        'status',
        'swap_from',
        'swap_to',
        'comment',
        'payment_method',
        'created_at',
    ];

    protected $casts = [
        'user_id',
        'amount' => 'float',
        'payment_method' => 'array',
    ];

    public function getQuerySelectables(): array
    {
        $table = $this->getTable();

        return [
            "{$table}.id",
            "{$table}.user_id",
            "{$table}.amount",
            "{$table}.transactable_id",
            "{$table}.transactable_type",
            "{$table}.type",
            "{$table}.status",
            "{$table}.comment",
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactable()
    {
        return $this->morphTo();
    }
}
