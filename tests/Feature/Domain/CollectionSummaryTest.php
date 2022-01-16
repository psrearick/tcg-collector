<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Models\Collection;

class CollectionSummaryTest extends CardCollectionTestCase
{
    public function test_a_collections_summary_is_updated_when_a_card_is_added() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $summary        = $collection->summary;
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $totalPrice     = $cardPrice * 2;

        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(2, $summary->total_cards);

        $this->createCollectionCard($uuid, 2, '', 3);
        $collection->refresh();

        $collectionCard     = $collection->cards->get(1);
        $summary            = $collection->summary;
        $cardPrice          = $collectionCard->pivot->price_when_added;
        $newCardTotal       = $cardPrice * 3;
        $collectionPrice    = $totalPrice + $newCardTotal;

        $this->assertEquals($collectionPrice, $summary->current_value);
        $this->assertEquals(5, $summary->total_cards);
    }

    public function test_adding_multiple_cards_updates_the_collection_summary() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $summary        = $collection->summary;
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $totalPrice     = $cardPrice * 2;

        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(2, $summary->total_cards);

        $this->createCollectionCard($uuid, 0, '', 3);

        $collection->refresh();

        $this->assertEquals(2, $collection->cards->count());

        $collectionCard     = $collection->cards->last();
        $summary            = $collection->summary;
        $cardPrice          = $collectionCard->pivot->price_when_added;
        $newCardTotal       = $cardPrice * 3;
        $collectionPrice    = $totalPrice + $newCardTotal;

        $this->assertEquals($collectionPrice, $summary->current_value);
        $this->assertEquals(5, $summary->total_cards);

        $this->createCollectionCard($uuid, 2, '', 7);

        $collection->refresh();

        $this->assertEquals(3, $collection->cards->count());

        $collectionCard     = $collection->cards->last();
        $summary            = $collection->summary;
        $cardPrice          = $collectionCard->pivot->price_when_added;
        $newCardTotal       = $cardPrice * 7;
        $collectionPrice    = $collectionPrice + $newCardTotal;

        $this->assertEquals($collectionPrice, $summary->current_value);
        $this->assertEquals(12, $summary->total_cards);
    }
}
