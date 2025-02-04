<?php

namespace App\Services\User;

use Illuminate\Support\Str;
use App\Contracts\Repositories\SavingsAccountRepositoryInterface;

class SavingsAccountService
{
    protected $repository;

    public function __construct(SavingsAccountRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function query()
    {
        return $this->repository->query();
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getById($id)
    {
        return $this->repository->findById($id);
    }

    public function create($data)
    {
        $data['slug'] = Str::slug($data['name']); // Auto-generate slug
        $data['country_id'] = json_encode($data['country_id']); // Store as JSON
        return $this->repository->store($data);
    }

    public function update($id, $data)
    {
        $data['slug'] = Str::slug($data['name']); // Update slug
        $data['country_id'] = json_encode($data['country_id']); // Convert to JSON
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
