<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Aggregate\Actions\MoveCollectionCards;
use App\Domain\Collections\Models\Collection;

class CollectionSummaryTest extends CardCollectionTestCase
{
    public function test_a_collections_summary_is_updated_when_a_card_is_added() : void
    {
        // set user
        $this->act();

        // create collection
        $uuid = $this->createCollection();

        // add card to collection
        $this->createCollectionCard($uuid, 0, '', 2);

        // get collection card and summary
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $totalPrice     = $cardPrice * 2;
        $summary        = $collection->summary;

        // ASSERT: Collection summary is updated
        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(2, $summary->total_cards);

        // add another card to the collection
        $this->createCollectionCard($uuid, 2, '', 3);

        // refresh to collection instance
        $collection->refresh();

        // get new card and summary
        $collectionCard     = $collection->cards->get(1);
        $cardPrice          = $collectionCard->pivot->price_when_added;
        $newCardTotal       = $cardPrice * 3;
        $collectionPrice    = $totalPrice + $newCardTotal;
        $summary            = $collection->summary;

        $this->assertEquals($collectionPrice, $summary->current_value);
        $this->assertEquals(5, $summary->total_cards);
    }

    public function test_a_collections_summary_is_updated_when_a_card_is_deleted() : void
    {
        // set user
        $this->act();

        // create collection in folder
        $folderCollection   = $this->createCollectionInFolder();
        $collectionUuid     = $folderCollection['collection_uuid'];
        $folderUuid         = $folderCollection['folder_uuid'];

        // add cards to collection
        $c1 = $this->createCollectionCard($collectionUuid, 1);
        $c2 = $this->createCollectionCard($collectionUuid, 2);
        $c3 = $this->createCollectionCard($collectionUuid, 3);

        // get model
        $collection = Collection::uuid($collectionUuid);

        // get state
        $state1 = $this->getCollectionSummary($collection);

        // assert quantity
        $this->assertEquals(3, $state1['total_cards']);

        // delete cards
        $cardsToDelete = $collection->cardSummaries()
            ->whereIn('card_uuid', [$c2, $c3])
            ->get()
            ->toArray();

        $this->deleteCards($collectionUuid, $cardsToDelete);

        // get state
        $state2 = $this->getCollectionSummary($collection);

        // assert quantity changed
        $this->assertEquals(1, $state2['total_cards']);
    }

    public function test_a_collections_summary_is_updated_when_a_card_is_moved() : void
    {
        // set user
        $this->act();

        // create collection
        $uuid = $this->createCollection();

        // add card to collection
        $this->createCollectionCard($uuid, 0, '', 2);

        // get collection card and summary
        $collection     = Collection::uuid($uuid);
        $cards          = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        $totalPrice     = $cards->sum('price_when_added');
        $totalQuantity  = $cards->sum('quantity');
        $summary        = $collection->summary;

        // ASSERT: Collection summary is updated
        $this->assertEquals($totalPrice * $totalQuantity, $summary->current_value);
        $this->assertEquals($totalQuantity, $summary->total_cards);

        // create new collection
        $collection2Uuid = $this->createCollection();
        $collection2     = collection::uuid($collection2Uuid);
        $cards2          = $collection2->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection is empty
        $this->assertequals(0, $cards2->sum('quantity'));

        // move card from collection 1 to collection 2
        (new MoveCollectionCards)($uuid, $collection2Uuid, $cards->toArray());

        // refresh to collection instance
        $collection->refresh();
        $collection2->refresh();

        // get new card and summaries
        $cards = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        $totalPrice = $cards->reduce(function ($carry, $card) {
            return $carry + ($card->quantity * $card->price_when_added);
        }, 0);

        $totalQuantity  = $cards->sum('quantity');
        $summary        = $collection->summary;

        $cards2 = $collection2->cards->map(function ($card) {
            return $card->pivot;
        });

        $totalPrice2 = $cards2->reduce(function ($carry, $card) {
            return $carry + ($card->quantity * $card->price_when_added);
        }, 0);

        $totalQuantity2 = $cards2->sum('quantity');
        $summary2       = $collection2->summary;

        // assert card quantities changed
        $this->assertEquals($totalQuantity, $summary->total_cards);
        $this->assertEquals($totalQuantity2, $summary2->total_cards);

        // assert card prices changed
        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals($totalPrice2, $summary2->current_value);
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
