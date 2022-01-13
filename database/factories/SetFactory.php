<?php

namespace Database\Factories;

use App\Domain\Sets\Models\Set;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SetFactory extends Factory
{
    protected $model = Set::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'setId'         => Str::uuid()->toString(),
            'name'          => $this->faker->words(3, true),
            'code'          => $this->faker->text(5),
            'release_date'  => $this->faker->date(),
        ];
    }
}
