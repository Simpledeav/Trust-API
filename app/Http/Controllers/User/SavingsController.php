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
use App\Models\SavingsLedger as ModelsSavingsLedger;
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
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $savingsHistory = QueryBuilder::for(
            ModelsSavingsLedger::query()->where('user_id', $request->user()->id)
        )
            // Exclude fields directly in the select
            ->select([
                'id',
                'user_id',
                'savings_id',
                'amount',
                'type',
                'method',
                'comment',
                'created_at',
            ])
            ->allowedFilters([
                'type',
                'method',
                AllowedFilter::scope('creation_date'),
                AllowedFilter::exact('amount'),
                AllowedFilter::exact('savings_id'),
            ])
            ->with([
                'savings' => function ($query) {
                    $query->select(['id', 'savings_account_id'])
                        ->with('savingsAccount:id,name,title');  // Include only name & title
                }
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['amount', 'type', 'method', 'created_at'])
            ->paginate((int) $request->per_page)
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Savings Transactions fetched successfully')
            ->withData(['savings_history' => $savingsHistory])
            ->build();
    }

    public function fetch(Request $request): Response
    {
        // Get the IDs of savings accounts the user already has
        $userSavingsAccountIds = $request->user()->savings()
            ->pluck('savings_account_id')
            ->toArray();

        $accounts = QueryBuilder::for(SavingsAccount::class)
            ->whereNotIn('id', $userSavingsAccountIds)  // Exclude accounts user already has
            ->allowedFilters([
                'name',
                'status',
                AllowedFilter::exact('rate'),
                AllowedFilter::scope('country_id'),
            ])
            ->allowedSorts(['name', 'rate', 'created_at'])
            ->defaultSort('name')
            ->paginate((int) $request->per_page)
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Available savings accounts fetched successfully')
            ->withData(['accounts' => $accounts])
            ->build();
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
            $method = 'contribution';
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
            $method = 'contribution';
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
