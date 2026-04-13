<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'address' => fake()->optional()->streetAddress(),
            'address2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->optional()->city(),
            'state' => fake()->optional()->state(),
            'country' => fake()->optional()->countryCode(),
            'phone' => fake()->optional()->phoneNumber(),
            'fax' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'contact' => fake()->optional()->name(),
            'notes' => fake()->optional()->sentence(),
            'zip' => fake()->optional()->postcode(),
            'url' => fake()->optional()->url(),
            'image' => null,
        ];
    }
}
