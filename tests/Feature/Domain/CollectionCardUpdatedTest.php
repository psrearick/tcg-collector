<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Aggregate\Actions\DeleteCollectionCards;
use App\Domain\Collections\Aggregate\Actions\MoveCollectionCards;

use App\Domain\Collections\Models\Collection;

class CollectionCardUpdatedTest extends CardCollectionTestCase
{
    public function test_a_collection_card_can_be_deleted() : void
    {
        $this->act();

        // create collection and add a card to it
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid);

        // get collection card values
        $collection = Collection::uuid($collectionUuid);
        $cards      = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        // one card was added
        $this->assertEquals(1, $cards->sum('quantity'));

        // delete card from collection
        (new DeleteCollectionCards)($collectionUuid, $cards->toArray());

        // refresh data
        $collection->refresh();

        $cards = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection is empty
        $this->assertequals(0, $cards->sum('quantity'));
    }

    public function test_a_collection_card_can_be_moved() : void
    {
        $this->act();

        // create collection and add a card to it
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid);

        // get collection card values
        $collection = Collection::uuid($collectionUuid);
        $cards      = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        // one card was added
        $this->assertEquals(1, $cards->sum('quantity'));

        // create new collection
        $collection2Uuid = $this->createCollection();
        $collection2     = collection::uuid($collection2Uuid);
        $cards2          = $collection2->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection is empty
        $this->assertequals(0, $cards2->sum('quantity'));

        // move card from collection 1 to collection 2
        (new MoveCollectionCards)($collectionUuid, $collection2Uuid, $cards->toArray());

        // refresh data
        $collection->refresh();
        $collection2->refresh();

        $cards = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        $cards2 = $collection2->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection 1 is empty
        $this->assertequals(0, $cards->sum('quantity'));

        // one card was added to collection 2
        $this->assertEquals(1, $cards2->sum('quantity'));
    }

    public function test_collection_cards_can_be_deleted() : void
    {
        $this->act();

        // create collection and add a card to it
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid);
        $this->createCollectionCard($collectionUuid);
        $this->createCollectionCard($collectionUuid);
        $this->createCollectionCard($collectionUuid);

        // get collection card values
        $collection = Collection::uuid($collectionUuid);
        $cards      = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        // 4 cards were added
        $this->assertEquals(4, $cards->sum('quantity'));

        // delete 2 cards from collection
        $move = $cards->take(2);
        (new DeleteCollectionCards)($collectionUuid, $move->toArray());

        // refresh data
        $collection->refresh();

        $cards = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection has two less cards
        $this->assertequals(2, $cards->sum('quantity'));
    }

    public function test_collection_cards_can_be_moved() : void
    {
        $this->act();

        // create collection and add a card to it
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid);
        $this->createCollectionCard($collectionUuid);
        $this->createCollectionCard($collectionUuid);
        $this->createCollectionCard($collectionUuid);

        // get collection card values
        $collection = Collection::uuid($collectionUuid);
        $cards      = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        // 4 cards were added
        $this->assertEquals(4, $cards->sum('quantity'));

        // create new collection
        $collection2Uuid = $this->createCollection();
        $collection2     = collection::uuid($collection2Uuid);
        $cards2          = $collection2->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection is empty
        $this->assertequals(0, $cards2->sum('quantity'));

        // move 2 cards from collection 1 to collection 2
        $move = $cards->take(2);
        (new MoveCollectionCards)($collectionUuid, $collection2Uuid, $move->toArray());

        // refresh data
        $collection->refresh();
        $collection2->refresh();

        $cards = $collection->cards->map(function ($card) {
            return $card->pivot;
        });

        $cards2 = $collection2->cards->map(function ($card) {
            return $card->pivot;
        });

        // collection 1 has two less cards
        $this->assertequals(2, $cards->sum('quantity'));

        // one card was added to collection 2
        $this->assertEquals(2, $cards2->sum('quantity'));
    }
}
