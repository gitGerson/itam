<?php

namespace Database\Factories;

use App\Models\CustomFieldset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomFieldset>
 */
class CustomFieldsetFactory extends Factory
{
    protected $model = CustomFieldset::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'notes' => fake()->optional()->sentence(),
            'repeatable' => fake()->boolean(),
        ];
    }
}
