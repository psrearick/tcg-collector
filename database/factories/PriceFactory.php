<?php

namespace Database\Factories;

use App\Domain\Prices\Models\Price;
use App\Domain\Prices\Models\PriceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        $types = [
            'usd',
            'usd_foil',
            'usd_etched',
        ];

        return [
            'card_uuid'     => '',
            'provider_uuid' => PriceProvider::where('name', '=', 'scryfall')->first()->uuid,
            'price'         => $this->faker->numberBetween(0, 9999),
            'type'          => $types[$this->faker->numberBetween(0, 2)],
        ];
    }
}
