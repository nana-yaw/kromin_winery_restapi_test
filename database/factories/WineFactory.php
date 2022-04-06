<?php

namespace Database\Factories;

use App\Utils;
use App\Models\Wine;
use Illuminate\Database\Eloquent\Factories\Factory;

class WineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
            'code' => Utils::genUuid(),
            'colour' => $this->faker->colorName,
            'effervescence' => $this->faker->randomElement(['fermo', 'frizzante', 'spumante']),
            'sweetness' => $this->faker->randomElement(['secco', 'abboccato', 'brut', 'dolce', 'extra-brut', 'dosaggio zero', 'extra-dry', 'amabile']),
            'year' => $this->faker->numberBetween($min = 1, $max = 100),
        ];
    }
}
