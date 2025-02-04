<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $state = State::inRandomOrder()->first();
        $city = City::inRandomOrder()->first();
        $currency = Currency::inRandomOrder()->first();
        
        $user = User::create([
            'id' => Str::uuid(),
            'country_id' => $state->country_id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'currency_id' => $currency->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main Street',
            'zipcode' => '12345',
            'ssn' => '123-45-6789',
            'dob' => now()->subYears(30),
            'nationality' => 'American',
            'experience' => '5 years',
            'employed' => 'Yes',
            'status' => 'active',
            'kyc' => 'approved',
            'id_number' => 'A1234567',
            'front_id' => 'path/to/front_id.jpg',
            'back_id' => 'path/to/back_id.jpg',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'blocked_at' => null,
        ]);

        Wallet::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'balance' => 0, // Default balance
        ]);

        // Generate additional fake users
        // User::factory()->count(10)->create();
    }
}
