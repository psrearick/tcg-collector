<?php

namespace Tests\Unit\Domain\Cards\Presenters;

use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Presenters\PrintingsPresenter;
use App\Domain\Prices\Models\Price;
use App\Domain\Sets\Models\Set;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\Feature\Domain\CardCollectionTestCase;

/** @see PrintingsPresenter */
class PrintingsPresenterTest extends CardCollectionTestCase
{
    public function testPresent() : void
    {
        $oracleId   = Str::uuid()->toString();
        $name       = $this->faker->name();

        $sets = Set::factory()
            ->has(
                Card::factory()
                    ->state(function () use ($oracleId, $name) {
                        return [
                            'oracleId'  => $oracleId,
                            'name'      => $name,
                        ];
                    })
            )
            ->count(3)
            ->create();
        assert($sets instanceof Collection);

        $types = ['usd', 'usd_foil', 'usd_etched'];

        foreach ($sets as $set) {
            $cardUuid = $set->cards->first()->uuid;
            foreach ($types as $type) {
                Price::factory()->create([
                    'card_uuid' => $cardUuid,
                    'type'      => $type,
                ]);
            }
        }

        $reference      = $sets->first()->cards->first();
        $presentation   = (new PrintingsPresenter($oracleId))->present();

        $this->assertCount(count($sets), $presentation);

        foreach ($presentation as $item) {
            $this->assertEquals($oracleId, $item->oracle_id);
            $this->assertEquals($reference->name, $item->name);
        }
    }
}
