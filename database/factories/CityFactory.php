<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_name' => $this->faker->city,
            'state_code' => $this->faker->stateAbbr,
            'country_code' => $this->faker->countryCode,
            'lat' => $this->faker->latitude,
            'lon' => $this->faker->longitude,
        ];
    }
}
