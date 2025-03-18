<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Enums\ApiErrorCode;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use App\Services\User\TransactionService;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use App\Http\Requests\User\StoreTransactionRequest;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use App\DataTransferObjects\Models\TransactionModelData;
use App\Http\Controllers\NotificationController as Notifications;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Models\Transaction $transaction
     */
    public function __construct(public Transaction $transaction)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $transactions = QueryBuilder::for(
            $this->transaction->query()->where('user_id', $request->user()->id)
        )
            ->allowedFields($this->transaction->getQuerySelectables()) // Get the selectable fields dynamically
            ->allowedFilters([
                'status',
                'type', // Filter by transaction type (credit, debit, transfer)
                AllowedFilter::scope('creation_date'), // Custom filter scope for creation date
                AllowedFilter::exact('amount'), // Exact match filter for amount
                AllowedFilter::exact('transactable_id'), // Exact match filter for transactable ID
                AllowedFilter::exact('transactable_type'), // Filter by the type of transactable model (e.g., Wallet, Savings, Trade)
                AllowedFilter::scope('comment'), // Scope for filtering by comment (if applicable)
            ])
            ->allowedIncludes([
                AllowedInclude::custom('user', new IncludeSelectFields([
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ])),
                AllowedInclude::custom('transactable', new IncludeSelectFields([
                    'id',
                    'type', // You can select more details depending on the model (e.g., Wallet, Trade, Savings)
                    'amount', // Amount associated with transactable
                    'status', // You can display status of the related model
                ])),
            ])
            ->defaultSort('-created_at') // Default sort by created_at descending
            ->allowedSorts([
                'status', // Sort by transaction status
                'type', // Sort by transaction type (credit, debit, transfer)
                'amount', // Sort by transaction amount
                'created_at', // Sort by creation date
                'updated_at', // Sort by updated date
            ])
            ->paginate((int) $request->per_page) // Paginate the results
            ->withQueryString(); // Retain query string for pagination links

        return ResponseBuilder::asSuccess()
            ->withMessage('Transactions fetched successfully')
            ->withData([
                'transactions' => $transactions,
            ])
            ->build();
    }

    /**
     * Create a new transaction.
     *
     * @param \App\Http\Requests\User\StoreTransactionRequest $request
     * @param \App\Services\TransactionService $transactionService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(
        StoreTransactionRequest $request,
        TransactionService $transactionService
    ): Response { 
    
        $user = $request->user();
        $wallet = $user->wallet;
        $balance = $user->wallet->getBalance('wallet'); // Get user wallet balance
        $amount = (float) $request->amount;
        $type = $request->type;
    
        // Check if transaction is a debit and if amount exceeds balance
        if ($type === 'debit' && $amount > $balance) {
            return ResponseBuilder::asError(ApiErrorCode::INSUFFICIENT_FUNDS->value)
                ->withMessage('Insufficient wallet balance.')
                ->build();
        }
    
        // Proceed with transaction creation
        $transaction = $transactionService->create(
            (new TransactionModelData())
                ->setUserId($user->id)
                ->setAmount($amount)
                ->setTransactableId($wallet->id)
                ->setTransactableType(Wallet::class)
                ->setType($type)
                ->setStatus('pending') 
                ->setSwapFrom('wallet') 
                ->setSwapTo(null)
                ->setComment($request->comment),
            $user
        );
    
        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_CREATED)
            ->withMessage('Transaction created successfully')
            ->withData(['transaction' => $transaction])
            ->build();
    }
    

    /**
     * Create a new transaction.
     *
     * @param \App\Http\Requests\User\StoreTransactionRequest $request
     * @param \App\Services\TransactionService $transactionService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transfer(
        StoreTransactionRequest $request,
        TransactionService $transactionService
    ): Response { 
        $transaction = $transactionService->swap(
            (new TransactionModelData())
                ->setUserId($request->user()->id)
                ->setAmount((float) $request->amount)
                ->setTransactableId($request->user()->wallet->id)
                ->setTransactableType($request->transactable_type)
                ->setType($request->type)
                ->setStatus("pending")
                ->setSwapFrom($request->from)
                ->setSwapTo($request->to)
                ->setComment($request->comment),
            $request->user()
        );

        // Notifications::sendTestEmailNotification($request->user());

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_CREATED)
            ->withMessage('Transafer created successfully')
            ->withData([
                'transaction' => $transaction,
            ])
            ->build();
    }
}
