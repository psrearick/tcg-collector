<?php

namespace Tests\Feature\Domain;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Domain\Prices\Aggregate\Actions\createPrice;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use App\Domain\Prices\Models\Price;

class PriceTest extends CardCollectionTestCase
{
    public function test_a_collection_card_summary_is_updated_when_a_price_is_created() : void
    {
        // set user
        $this->act();

        // create collection card
        $collectionUuid = $this->createCollection();
        $cardUuid       = $this->createCollectionCard($collectionUuid);

        // get state
        $collection     = Collection::uuid($collectionUuid);
        $cardSummary    = $collection->cardSummaries->first();

        // create price data
        $priceData      = Price::factory()->make([
            'card_uuid' => $cardUuid,
            'type'      => (new MatchFinish)($cardSummary->finish),
        ])->toArray();

        // create price
        (new createPrice)($priceData);

        // state
        // stored
        $price      = $priceData['price'];

        // previous
        $acquired   = $cardSummary->price_when_added;
        $current    = $cardSummary->current_price;

        // updated
        $cardSummary->refresh();
        $acquired2  = $cardSummary->price_when_added;
        $current2   = $cardSummary->current_price;

        // assertions
        $this->assertEquals($acquired, $acquired2);
        $this->assertEquals($acquired, $current);
        $this->assertEquals($price, $current2);
        $this->assertNotEquals($price, $acquired);
    }

    public function test_a_price_can_be_created() : void
    {
        // set user
        $this->act();

        // get card
        $card       = Card::take(1)->first();

        // get card prices
        $prices     = $card->prices;

        // get price data
        $priceData  = Price::factory()->make([
            'card_uuid' => $card->uuid,
        ])->toArray();
        $type       = $priceData['type'];
        $price      = $priceData['price'];

        // create price
        (new createPrice)($priceData);

        // refresh card data
        $card->refresh();

        // get prices for comparisons
        $initialPrices      = $prices->where('type', '=', $type);
        $initialCount       = $initialPrices->count();
        $initialPrice       = $initialCount > 0
            ? $initialPrice = $initialPrices->last()->price
            : 0;

        $newPrices      = $card->prices->where('type', '=', $type);
        $newCount       = $newPrices->count();
        $newPrice       = optional($newPrices->last())->price;

        // assertions
        $this->assertNotNull($newPrice);
        $this->assertNotEquals($initialPrice, $newPrice);
        $this->assertGreaterThan($initialCount, $newCount);
        $this->assertEquals($price, $newPrice);
    }

    // public function test_a_collection_summary_is_updated_when_a_price_is_created() : void
    // {}

    // public function test_a_parent_folder_summary_is_updated_when_a_childs_price_is_updated() : void
    // {}
}
