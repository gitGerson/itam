<?php

namespace Database\Factories;

use App\Models\StatusLabel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StatusLabel>
 */
class StatusLabelFactory extends Factory
{
    protected $model = StatusLabel::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'deployable' => fake()->boolean(),
            'pending' => fake()->boolean(),
            'archived' => false,
            'notes' => fake()->optional()->sentence(),
            'color' => fake()->hexColor(),
            'show_in_nav' => fake()->boolean(),
            'default_label' => false,
        ];
    }
}
