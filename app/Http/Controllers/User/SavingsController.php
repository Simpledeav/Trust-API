<?php

namespace App\Http\Controllers\User;

use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Models\SavingsAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\SavingsLedger;
use App\Http\Requests\SavingsRequest;
use App\Services\User\SavingsService;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use App\Http\Requests\SavingsLedgerRequest;
use App\Services\User\SavingsAccountService;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class SavingsController extends Controller
{
    protected SavingsService $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    /**
     * Display a listing of savings accounts.
     */
    public function store(SavingsRequest $request): Response
    {
        $savingsAccount = new SavingsAccount($request->validated());

        $account = $this->savingsService->create($request->user(), $savingsAccount);

        return ResponseBuilder::asSuccess()
            ->withMessage('Savings account assigned successfully')
            ->withData(['account' => $account])
            ->build();
    }

    public function credit(SavingsLedgerRequest $request): Response
    {
        try {
            $user = $request->user();
            $savingsAccount = $request->savings_account_id;
            $amount = $request->amount;
            $method = $request->method;
            $comment = $request->comment;

            $balance = $this->savingsService->credit($user, $savingsAccount, $amount, $method, $comment);

            return ResponseBuilder::asSuccess()
                ->withMessage('Savings credited successfully')
                ->withData(['balance' => $balance])
                ->build();

        } catch (\Exception $e) {

            return ResponseBuilder::asError(ApiErrorCode::GENERAL_ERROR->value)
                ->withMessage($e->getMessage() ?: 'Unable to process savings credit.')
                ->build();
                
        }
    }

    public function debit(SavingsLedgerRequest $request): Response
    {
        try {
            $user = $request->user();
            $savingsAccount = $request->savings_account_id;
            $amount = $request->amount;
            $method = $request->method;
            $comment = $request->comment;

            $balance = $this->savingsService->debit($user, $savingsAccount, $amount, $method, $comment);

            return ResponseBuilder::asSuccess()
                ->withMessage('Savings debited successfully')
                ->withData(['balance' => $balance])
                ->build();

        } catch (\Exception $e) {

            return ResponseBuilder::asError(ApiErrorCode::GENERAL_ERROR->value)
                ->withMessage($e->getMessage() ?: 'Unable to process savings credit.')
                ->build();
                
        }
    }
}
