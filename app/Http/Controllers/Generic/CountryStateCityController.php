<?php

namespace App\Http\Controllers\Generic;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Nationalities;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class CountryStateCityController extends Controller
{
    /**
     * Fetch all countries with filters.
     */
    public function countries(Request $request): Response
    {
        $countries = QueryBuilder::for(Country::class)
            ->allowedFilters([
                'name',
                'phone_code',
                AllowedFilter::exact('status'),
            ])
            ->allowedIncludes(['states'])
            ->defaultSort('name')
            ->get();

        return ResponseBuilder::asSuccess()
            ->withMessage('Countries fetched successfully.')
            ->withData(['countries' => $countries])
            ->build();
    }

    /**
     * Fetch all states with filters.
     */
    public function states(Request $request): Response
    {
        $states = QueryBuilder::for(State::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status'),
                AllowedFilter::exact('country_id'),
            ])
            ->allowedIncludes(['country', 'cities'])
            ->defaultSort('name')
            ->get();

        return ResponseBuilder::asSuccess()
            ->withMessage('States fetched successfully.')
            ->withData(['states' => $states])
            ->build();
    }

    /**
     * Fetch all cities with filters.
     */
    public function cities(Request $request): Response
    {
        $cities = QueryBuilder::for(City::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status'),
                AllowedFilter::exact('state_id'),
            ])
            ->allowedIncludes(['state'])
            ->defaultSort('name')
            ->get();

        return ResponseBuilder::asSuccess()
            ->withMessage('Cities fetched successfully.')
            ->withData(['cities' => $cities])
            ->build();
    }

     /**
     * Fetch all nationalities with filters.
     */
    public function nationalities(): Response
    {
        $nationalities = QueryBuilder::for(Nationalities::class)
            ->allowedFilters([
                'name',
            ])
            ->defaultSort('name')
            ->get();

        return ResponseBuilder::asSuccess()
            ->withMessage('Nationalities fetched successfully.')
            ->withData(['nationalities' => $nationalities])
            ->build();
    }
}
