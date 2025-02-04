<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            // You can uncomment these lines if you have related tables
            // 'country_id' => $this->faker->numberBetween(1, 10),
            // 'state_id' => $this->faker->numberBetween(1, 10),
            // 'city_id' => $this->faker->numberBetween(1, 10),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'username' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'zipcode' => $this->faker->postcode,
            'ssn' => $this->faker->ssn,
            'dob' => $this->faker->date('Y-m-d', '1993-01-01'),
            'nationality' => $this->faker->country,
            'experience' => $this->faker->numberBetween(1, 15) . ' years',
            'employed' => $this->faker->randomElement(['Yes', 'No']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'kyc' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
            'id_number' => strtoupper(Str::random(8)),
            'front_id' => $this->faker->imageUrl(640, 480, 'people'),
            'back_id' => $this->faker->imageUrl(640, 480, 'people'),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // You can change this to a custom password or use a dynamic one
            'blocked_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
