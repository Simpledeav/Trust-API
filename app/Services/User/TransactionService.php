<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Models\Transaction;
use App\DataTransferObjects\Models\TransactionModelData;

class TransactionService
{
    public function create(TransactionModelData $data, User $user): Transaction
    {
        return Transaction::query()->create($data->toArray())->refresh();
    }
}
