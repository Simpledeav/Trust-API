<?php

namespace App\Contracts\Repositories;

interface SavingsAccountRepositoryInterface
{
    public function query();
    public function all();
    public function findById(string $id);
    public function store(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
