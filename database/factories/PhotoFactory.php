<?php

namespace Database\Factories;

use App\Utils;
use App\Models\Wine;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Photo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = Utils::genUuid();
        $extension = $this->faker->randomElement(['jpeg','png','jpg']);
        $image_url = $this->faker->randomElement([
            'https://unsplash.com/photos/V27db_3Lvmo',
            'https://unsplash.com/photos/8ozNn_BMICQ',
            'https://unsplash.com/photos/BedV296uH4E',
            'https://unsplash.com/photos/6jMd7n0Qd2I',
            'https://unsplash.com/photos/KKv5cgmhyRg',
            'https://unsplash.com/photos/14asNeF7K2g',
        ]);

        return [
            'name' => $name,
            'extension' => $extension,
            'url' => $image_url,
            'wine_id' => $this->faker->numberBetween($min = 1, $max = 10),
        ];
    }
}
