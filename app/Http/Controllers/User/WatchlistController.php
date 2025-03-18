<?php

namespace App\Http\Controllers\User;

use App\Models\Asset;
use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Services\User\WatchlistService;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class WatchlistController extends Controller
{
    protected WatchlistService $watchlistService;

    public function __construct(WatchlistService $watchlistService)
    {
        $this->watchlistService = $watchlistService;
    }

    /**
     * Fetch the user's watchlist.
     */
    public function index(Request $request): Response
    {
        $watchlist = $this->watchlistService->fetch($request->user()->id);

        return ResponseBuilder::asSuccess()
            ->withMessage('Watchlist fetched successfully')
            ->withData(['watchlist' => $watchlist])
            ->build();
    }

    /**
     * Add asset to the watchlist.
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'asset_id'   => 'required|uuid|exists:assets,id',
            'comment'  => 'nullable|string',
        ]);

        $watchlist = $this->watchlistService->store(
            $request->user()->id,
            $request->asset_id,
            $request->comment
        );

        return ResponseBuilder::asSuccess()
            ->withMessage('Asset added to watchlist')
            ->withData(['watchlist' => $watchlist])
            ->build();
    }

    /**
     * Remove asset from the watchlist.
     */
    public function destroy(Request $request, $watchlistId): Response
    {
        $watchlist = $this->watchlistService->destroy($request->user()->id, $watchlistId);

        if ($watchlist) {
            return ResponseBuilder::asSuccess()
                ->withMessage('Watchlist deleted successfully')
                ->build();

        } else {
            return ResponseBuilder::asError(ApiErrorCode::GENERAL_ERROR->value)
                ->withMessage('Error trying to delete watchlist.')
                ->build();
        }
    }

    public function watchlistAssets(Request $request): Response
    {
        // Fetch the authenticated user's watchlist
        $watchlist = $this->watchlistService->fetch($request->user()->id);

        // Extract asset IDs from the watchlist
        $watchlistAssetIds = $watchlist->pluck('asset_id')->toArray();

        // Query assets with allowed fields, filters, and includes
        $assets = QueryBuilder::for(
            Asset::select([
                'id', 'symbol', 'name', 'img', 'price', 'type', 'changes_percentage', 'change',
                'day_low', 'day_high', 'year_low', 'year_high', 'market_cap', 'price_avg_50',
                'price_avg_200', 'exchange', 'volume', 'avg_volume', 'open', 'previous_close',
                'eps', 'pe', 'status', 'tradeable'
            ])
        )
            ->allowedFields([
                'id', 'symbol', 'name', 'img', 'price', 'type', 'changes_percentage', 'change',
                'day_low', 'day_high', 'year_low', 'year_high', 'market_cap', 'price_avg_50',
                'price_avg_200', 'exchange', 'volume', 'avg_volume', 'open', 'previous_close',
                'eps', 'pe', 'status', 'tradeable'
            ])
            ->allowedFilters([
                'symbol', 'name', 'type', 'exchange', 'status', 'tradeable',
                AllowedFilter::exact('id'),
                AllowedFilter::scope('min_price'),
                AllowedFilter::scope('max_price'),
                AllowedFilter::scope('market_cap_range'),
                AllowedFilter::scope('volume_range')
            ])
            ->allowedIncludes([
                AllowedInclude::custom('related_assets', new IncludeSelectFields([
                    'id', 'symbol', 'name', 'price', 'type'
                ])),
            ])
            ->defaultSort('symbol');

        // Fetch assets with or without pagination
        $assets = $request->do_not_paginate
            ? $assets->get()
            : $assets->paginate((int) $request->per_page)->withQueryString();

        // Add `is_in_watchlist` field to each asset
        $assets->transform(function ($asset) use ($watchlistAssetIds) {
            $asset->is_in_watchlist = in_array($asset->id, $watchlistAssetIds);
            return $asset;
        });

        return ResponseBuilder::asSuccess()
            ->withMessage('Assets fetched successfully.')
            ->withData(['assets' => $assets])
            ->build();
    }
}
