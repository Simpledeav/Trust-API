<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Services\User\TradeService;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\User\ClosePositionRequest;
use App\Http\Requests\User\StorePositionRequest;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class PositionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Models\Position $position
     */
    public function __construct(public Position $position)
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $positions = QueryBuilder::for(
            $this->position->query()->where('user_id', $request->user()->id)
        )
            ->allowedFields($this->position->getQuerySelectables())
            ->allowedFilters([
                'status',
                'asset_type',
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
                'amount',
                'created_at',
                'updated_at',
            ])
            ->paginate((int) $request->per_page) 
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Positions fetched successfully')
            ->withData([
                'positions' => $positions,
            ])
            ->build();
    }

    /**
     * Display a listing of the trade history.
     */
    public function fetchTrades(Request $request): Response
    {
        $positions = QueryBuilder::for(
            Trade::query()->where('user_id', $request->user()->id)
        )
            ->allowedFilters([
                'status',
                'asset_type',
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
                'amount',
                'created_at',
                'updated_at',
            ])
            ->paginate((int) $request->per_page) 
            ->withQueryString();

            $transformedpositions = $positions->getCollection()->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'type' => $post->type,
                    'price' => $post->price,
                    'quantity' => $post->quantity,
                    'amount' => $post->amount,
                    'entry' => $post->entry,
                    'exit' => $post->exit,
                    'leverage' => $post->leverage,
                    'interval' => $post->interval,
                    'tp' => $post->tp,
                    'sl' => $post->sl,
                    'pl' => $post->pl,
                    'pl_percentage' => $post->pl_percentage,
                    'asset' => $post->asset ? [
                        'id' => $post->asset->id,
                        'name' => $post->asset->name,
                        'symbol' => $post->asset->symbol,
                        'img' => $post->asset->img,
                        'price' => $post->asset->price,
                        'type' => $post->asset->type,
                    ]: null,
                ];
            });

            $positions->setCollection($transformedpositions);

        return ResponseBuilder::asSuccess()
            ->withMessage('Positions history fetched successfully')
            ->withData([
                'positions' => $positions,
            ])
            ->build();
    }

    /**
     * Store a new position.
     *
     * @param \App\Http\Requests\User\StorePositionRequest $request
     * @param \App\Services\TradeService $tradeService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(
        StorePositionRequest $request,
        TradeService $tradeService
    ): Response {
        $position = $tradeService->createPosition(
            $request->validated(),
            $request->user()
        );

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_CREATED)
            ->withMessage('Opened position successfully')
            ->withData(['position' => $position])
            ->build();
    }

    /**
     * Close position.
     *
     * @param \App\Http\Requests\User\StorePositionRequest $request
     * @param \App\Models\Position $position
     * @param \App\Services\TradeService $tradeService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function close(
        Position $position,
        TradeService $tradeService,
        ClosePositionRequest $request
    ): Response {
        $positions = $tradeService->closePosition(
            $position,
            $request->user(),
            $request->validated()
        );

        return ResponseBuilder::asSuccess()
            ->withHttpCode(Response::HTTP_CREATED)
            ->withMessage('Position closed successfully')
            ->withData(['position' => $positions])
            ->build();
    }
}
