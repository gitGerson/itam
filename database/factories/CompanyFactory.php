<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'fax' => fake()->optional()->numerify('021#######'),
            'email' => fake()->optional()->companyEmail(),
            'phone' => fake()->optional()->numerify('08##########'),
            'image' => fake()->optional()->imageUrl(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
