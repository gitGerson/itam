<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Manufacturer>
 */
class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'url' => fake()->optional()->url(),
            'support_url' => fake()->optional()->url(),
            'warranty_lookup_url' => fake()->optional()->url(),
            'support_phone' => fake()->optional()->phoneNumber(),
            'support_email' => fake()->optional()->safeEmail(),
            'checkin_email' => true,
            'image' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
