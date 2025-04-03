<?php

namespace App\Http\Controllers;

use App\Models\Admin;
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
use App\Http\Controllers\NotificationController as Notifications;

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
                'account',
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
                    'change',
                    'changes_percentage',
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

            $positions->getCollection()->transform(function ($position) {
                // Basic calculations
                $currentValue = $position->asset->price * $position->quantity;
                $pl = ($currentValue - $position->amount) + $position->extra;
                $pl_percentage = $position->amount != 0 ? ($pl / $position->amount) * 100 : 0;
                
                // Initialize today's values with regular PL values by default
                $today_pl = $pl;
                $today_pl_percentage = $pl_percentage;
                
                // Only calculate today-specific PL if position is older than 24 hours
                if ($position->created_at->diffInHours(now()) >= 24) {
                    $today_pl = ($position->asset->change * $position->quantity) + $position->extra;
                    $today_pl_percentage = $position->amount != 0 ? ($today_pl / $position->amount) * 100 : 0;
                }
            
                $position->order_type = 'buy';
                $position->value = number_format($currentValue + $position->extra, 2);
                $position->pl = number_format($pl, 2);
                $position->pl_percentage = number_format($pl_percentage, 2);
                $position->today_pl = number_format($today_pl, 2);
                $position->today_pl_percentage = number_format($today_pl_percentage, 2);
                
                return $position;
            });

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

                if($post->status == 'open' && $post->type == 'buy') {
                    $pl = ($post->asset->price * $post->quantity) - $post->amount + $post->pl;
                    $pl_percent = ($pl / $post->amount) * 100;
                } else {
                    $pl = $post->pl;
                    $pl_percent = $post->pl_percentage;
                }

                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'type' => $post->type,
                    'price' => $post->price,
                    'quantity' => $post->quantity,
                    'amount' => $post->amount,
                    'status' => $post->status,
                    'entry' => $post->entry,
                    'exit' => $post->exit,
                    'leverage' => $post->leverage,
                    'interval' => $post->interval,
                    'tp' => $post->tp,
                    'sl' => $post->sl,
                    'pl' => number_format($pl, 2),
                    'pl_percentage' => number_format($pl_percent, 2),
                    'asset' => $post->asset ? [
                        'id' => $post->asset->id,
                        'name' => $post->asset->name,
                        'symbol' => $post->asset->symbol,
                        'img' => $post->asset->img,
                        'price' => $post->asset->price,
                        'type' => $post->asset->type,
                    ]: null,
                    'created_at' => $post->created_at,
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

        $admin = Admin::where('email', config('app.admin_mail'))->first();

        Notifications::sendPositionOpenedNotification($request->user(), $position, $position->asset, $request->wallet);
        NotificationController::sendAdminNewTradeNotification($admin, $request->user(), $position);

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
