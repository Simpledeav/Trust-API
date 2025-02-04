<?php

namespace App\Http\Controllers\Generic;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class AssetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Asset $asset
     */
    public function __construct(public Asset $asset)
    {
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $assets = QueryBuilder::for(
            $this->asset->select([
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

        $assets = $request->do_not_paginate
            ? $assets->get()
            : $assets->paginate((int) $request->per_page)->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Assets fetched successfully.')
            ->withData(['assets' => $assets])
            ->build();
    }
}
