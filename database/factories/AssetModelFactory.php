<?php

namespace Database\Factories;

use App\Models\AssetModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetModel>
 */
class AssetModelFactory extends Factory
{
    protected $model = AssetModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'model_number' => fake()->optional()->bothify('MDL-####'),
            'manufacturer_id' => null,
            'category_id' => null,
            'fieldset_id' => null,
            'eol' => fake()->optional()->numberBetween(1, 120),
            'image' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
