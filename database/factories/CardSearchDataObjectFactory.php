<?php

namespace Database\Factories;

use App\Models\CardSearchDataObject;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardSearchDataObjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CardSearchDataObject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'card_uuid'            => $this->faker->word,
            'card_name'            => $this->faker->word,
            'card_name_normalized' => $this->faker->word,
            'set_id'               => $this->faker->integer,
            'set_name'             => $this->faker->word,
            'set_code'             => $this->faker->word,
        ];
    }
}
