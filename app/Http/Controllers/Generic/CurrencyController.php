<?php

namespace App\Http\Controllers\Generic;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class CurrencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Currency $currency
     */
    public function __construct(public Currency $currency)
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
        $currencies = QueryBuilder::for(
            $this->currency->select([
                'id',
                'name',
                'symbol',
                'sign',
                'rate',
                'fees',
                'created_at',
                'updated_at'
            ])
        )
            ->allowedFields([
                'id', 'name', 'symbol', 'sign', 'rate', 'fees', 'created_at', 'updated_at'
            ])
            ->allowedFilters([
                'name',
                'symbol',
                AllowedFilter::exact('sign'),
            ])
            ->defaultSort('name');

        $currencies = $request->do_not_paginate
            ? $currencies->get()
            : $currencies->paginate((int) $request->per_page)->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Currencies fetched successfully.')
            ->withData([ 'currencies' => $currencies ])
            ->build();
    }
}
