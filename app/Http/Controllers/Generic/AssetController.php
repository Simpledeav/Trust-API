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
                AllowedFilter::scope('price_max', 'max_price'),
                AllowedFilter::scope('price_min', 'min_price'),
                AllowedFilter::scope('market_cap_min', 'marketCapMin'),
                AllowedFilter::scope('market_cap_max', 'marketCapMax'),
                AllowedFilter::scope('volume_min', 'volumeMin'),
                AllowedFilter::scope('volume_max', 'volumeMax'),
                AllowedFilter::callback('symbol', function ($query, $value) {
                    $query->where('symbol', 'like', "%{$value}%");
                }),
                AllowedFilter::callback('name', function ($query, $value) {
                    $query->where('name', 'like', "%{$value}%");
                }),
            ])
            ->allowedSorts([
                'price', 'market_cap', 'volume', 'changes_percentage', 'symbol', 'name'
            ])
            ->allowedIncludes([
                AllowedInclude::custom('related_assets', new IncludeSelectFields([
                    'id', 'symbol', 'name', 'price', 'type'
                ])),
            ]);

        // Apply default sort if no sort is specified
        if (!$request->has('sort')) {
            $assets->defaultSort('symbol');
        }

        // Handle pagination
        $assets = $request->boolean('do_not_paginate', false)
            ? $assets->get()
            : $assets->paginate((int) $request->input('per_page', 20))->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Assets fetched successfully.')
            ->withData(['assets' => $assets])
            ->build();
    }
}
