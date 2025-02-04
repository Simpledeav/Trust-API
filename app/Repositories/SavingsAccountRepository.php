<?php

namespace App\Repositories;

use App\Models\SavingsAccount;
use App\Contracts\Repositories\SavingsAccountRepositoryInterface;

class SavingsAccountRepository implements SavingsAccountRepositoryInterface
{
    public function query()
    {
        return SavingsAccount::query(); // Returns an Eloquent query builder instance
    }

    public function all()
    {
        return SavingsAccount::with('countries')->get();
    }

    public function findById(string $id)
    {
        return SavingsAccount::with('countries')->findOrFail($id);
    }

    public function store(array $data)
    {
        return SavingsAccount::create($data);
    }

    public function update(string $id, array $data)
    {
        $account = SavingsAccount::findOrFail($id);
        $account->update($data);
        return $account;
    }

    public function delete(string $id)
    {
        return SavingsAccount::destroy($id);
    }
}
