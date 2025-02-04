<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'id' => '356c74ed-1353-47ea-b050-911096eae5e7',
                'name' => 'United States Dollar',
                'symbol' => 'USD',
                'sign' => '$',
                'rate' => 1.00, // Base currency
                'fees' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Euro',
                'symbol' => 'EUR',
                'sign' => '€',
                'rate' => 0.92, // Example conversion rate to USD
                'fees' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'British Pound Sterling',
                'symbol' => 'GBP',
                'sign' => '£',
                'rate' => 0.80, // Example conversion rate to USD
                'fees' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Canadian Dollar',
                'symbol' => 'CAD',
                'sign' => 'C$',
                'rate' => 1.34, // Example conversion rate to USD
                'fees' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
