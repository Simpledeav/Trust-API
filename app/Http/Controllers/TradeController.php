<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Services\User\TradeService;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use App\Http\Requests\User\StoreTradeRequest;
use App\Http\Requests\User\UpdateTradeRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class TradeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Models\Trade $trade
     */
    public function __construct(public Trade $trade)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $transactions = QueryBuilder::for(
            $this->trade->query()->where('user_id', $request->user()->id)
        )
            ->allowedFields($this->trade->getQuerySelectables())
            ->allowedFilters([
                'status',
                'type',
                'asset_type',
                'account',
                AllowedFilter::scope('creation_date'), 
                AllowedFilter::exact('amount'),
                AllowedFilter::scope('asset_id'),
            ])
            ->allowedIncludes([
                AllowedInclude::custom('user', new IncludeSelectFields([
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ])),
                AllowedInclude::custom('asset', new IncludeSelectFields([
                    'id',
                    'name',
                    'symbol',
                    'img',
                    'price',
                    'type',
                ])),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                'status',
                'type',
                'amount',
                'created_at',
                'updated_at',
            ])
            ->paginate((int) $request->per_page) 
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Transactions fetched successfully')
            ->withData([
                'transactions' => $transactions,
            ])
            ->build();
    }

    /**
     * Store a new trade.
     *
     * @param \App\Http\Requests\User\StoreTradeRequest $request
     * @param \App\Services\TradeService $tradeService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(
        StoreTradeRequest $request,
        TradeService $tradeService
    ): Response {
        $trade = $tradeService->create(
            $request->validated(),
            $request->user()
        );

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_CREATED)
            ->withMessage('Trade created successfully')
            ->withData(['trade' => $trade])
            ->build();
    }

    /**
     * Update an existing trade.
     *
     * @param \App\Http\Requests\User\UpdateTradeRequest $request
     * @param \App\Models\Trade $trade
     * @param \App\Services\TradeService $tradeService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(
        UpdateTradeRequest $request,
        Trade $trade,
        TradeService $tradeService
    ): Response {
        $trade = $tradeService->update(
            $trade,
            $request->validated()
        );

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_OK)
            ->withMessage('Trade updated successfully')
            ->withData(['trade' => $trade])
            ->build();
    }
    
    /**
     * Update an existing trade.
     *
     * @param \App\Http\Requests\User\UpdateTradeRequest $request
     * @param \App\Models\Trade $trade
     * @param \App\Services\TradeService $tradeService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleStatus(Trade $trade, TradeService $tradeService, UpdateTradeRequest $request,): Response
    {
        $trade = $tradeService->toggleStatus($trade, $request->user(), $request);

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_OK)
            ->withMessage('Trade status updated successfully')
            ->withData(['trade' => $trade])
            ->build();
    }

    /**
     * Delete a trade.
     *
     * @param \App\Models\Trade $trade
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Trade $trade): Response
    {
        $trade->delete();

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_OK)
            ->withMessage('Trade deleted successfully')
            ->build();
    }
}
