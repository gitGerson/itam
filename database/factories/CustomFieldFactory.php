<?php

namespace Database\Factories;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomField>
 */
class CustomFieldFactory extends Factory
{
    protected $model = CustomField::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $element = fake()->randomElement(array_keys(CustomField::elementOptions()));
        $hasValues = in_array($element, CustomField::elementsWithValues(), true);

        return [
            'name' => fake()->unique()->words(2, true),
            'element' => $element,
            'field_values' => $hasValues
                ? implode("\n", fake()->words(4))
                : null,
            'help_text' => fake()->optional()->sentence(),
            'field_encrypted' => false,
            'show_in_email' => fake()->boolean(20),
        ];
    }
}
