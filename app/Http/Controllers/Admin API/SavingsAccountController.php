<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use App\Http\Requests\SavingsAccountRequest;
use App\Http\Requests\SavingsRequest;
use App\Services\User\SavingsAccountService;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class SavingsAccountController extends Controller
{
    protected SavingsAccountService $service;

    public function __construct(SavingsAccountService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of savings accounts.
     */
    public function index(Request $request): Response
    {
        $accounts = QueryBuilder::for($this->service->query())
            ->allowedFilters([
                'status',
                AllowedFilter::exact('balance'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::scope('created_at'),
            ])
            ->allowedIncludes([
                AllowedInclude::custom('user', new IncludeSelectFields([
                    'id',
                    'name',
                    'email',
                ])),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['balance', 'created_at', 'updated_at'])
            ->paginate((int) $request->per_page)
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Savings accounts fetched successfully')
            ->withData(['accounts' => $accounts])
            ->build();
    }

    /**
     * Display the specified savings account.
     */
    public function show(int $id): Response
    {
        $account = $this->service->getById($id);
        
        return ResponseBuilder::asSuccess()
            ->withMessage('Savings account retrieved successfully')
            ->withData(['account' => $account])
            ->build();
    }

    /**
     * Store a newly created savings account.
     */
    public function store(SavingsAccountRequest $request): Response
    {
        $account = $this->service->create($request->validated());

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_CREATED)
            ->withMessage('Savings account created successfully')
            ->withData(['account' => $account])
            ->build();
    }

    /**
     * Update the specified savings account.
     */
    public function update(SavingsAccountRequest $request, int $id): Response
    {
        $account = $this->service->update($id, $request->validated());

        return ResponseBuilder::asSuccess()
            ->withMessage('Savings account updated successfully')
            ->withData(['account' => $account])
            ->build();
    }

    /**
     * Remove the specified savings account.
     */
    public function destroy(int $id): Response
    {
        $this->service->delete($id);

        return ResponseBuilder::asSuccess()
            ->withMessage('Savings account deleted successfully')
            ->build();
    }
}
