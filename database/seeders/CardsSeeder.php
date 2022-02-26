<?php

namespace Database\Seeders;

use App\Domain\CardAttributes\Models\Finish;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use App\Domain\Prices\Models\Price;
use App\Domain\Prices\Models\PriceProvider;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CardsSeeder extends Seeder
{
    private $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = new Generator();

        DB::unprepared(file_get_contents(database_path('seeders/SeedCards.sql')));

        $provider = PriceProvider::create([
            'name'  => 'scryfall',
            'uuid'  => Str::uuid(),
        ]);

        Card::get()->each(function ($card) use ($provider) {
            $card->finishes->each(function (Finish $finish) use ($card, $provider) {
                Price::create([
                    'card_uuid'     => $card->uuid,
                    'provider_uuid' => $provider->uuid,
                    'type'          => app(MatchFinish::class)->execute($finish->name),
                    'price'         => $this->faker->numberBetween(5, 5000),
                ]);
            });
        });
    }
}
