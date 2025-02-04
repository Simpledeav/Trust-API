<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\DataProviders\CityDataProvider;
use App\DataProviders\StateDataProvider;
use App\DataProviders\CountryDataProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountryStateCityTableSeeder extends Seeder
{
    public function run()
    {
        // Seed countries
        $countries = CountryDataProvider::data();
        $countryIds = [];

        foreach ($countries as $countryData) {
            // Generate UUID for country
            $countryId = Str::uuid()->toString();

            // Store country data with UUID
            $countryModel = Country::create([
                'id' => $countryId,
                'name' => $countryData['name'],
                'phone_code' => $countryData['phone_code'],
                'status' => 'active'
            ]);

            // Store the country ID for reference
            $countryIds[$countryData['id']] = $countryId;
        }

        // Seed states
        $states = StateDataProvider::data();
        $stateIds = [];

        foreach ($states as $stateData) {
            if (isset($countryIds[$stateData['country_id']])) {
                // Get the country UUID reference
                $countryId = $countryIds[$stateData['country_id']];

                // Generate UUID for state
                $stateId = Str::uuid()->toString();

                // Store state data with UUID and reference country ID
                $stateModel = State::create([
                    'id' => $stateId,
                    'country_id' => $countryId,
                    'name' => $stateData['name'],
                    'status' => 'active'
                ]);

                // Store the state ID for reference
                $stateIds[$stateData['id']] = $stateId;
            }
        }

        // Seed cities
        $cities = CityDataProvider::data();

        foreach ($cities as $cityData) {
            if (isset($stateIds[$cityData['state_id']])) {
                // Get the state UUID reference
                $stateId = $stateIds[$cityData['state_id']];

                // Generate UUID for city
                $cityId = Str::uuid()->toString();

                // Store city data with UUID and reference state ID
                City::create([
                    'id' => $cityId,
                    'state_id' => $stateId,
                    'name' => $cityData['name'],
                    'status' => 'active'
                ]);
            }
        }
    }
}
