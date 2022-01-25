<?php

namespace Tests\Feature\Domain;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;

class CardCollectionTest extends CardCollectionTestCase
{
    public function test_a_basic_card_can_be_added_to_a_new_collection() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createBasicCollectionCard($uuid, 0, '', 2);
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();

        $this->assertEquals(2, $collectionCard->pivot->quantity);
        $this->assertCount(1, $collection->cards);
        $this->assertCount(1, $collection->cardSummaries);
    }

    public function test_a_card_can_be_added_to_a_new_collection() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();

        $this->assertEquals(2, $collectionCard->pivot->quantity);
        $this->assertEquals(1, $collection->cards->count());
        $this->assertCount(1, $collection->cardSummaries);
    }

    public function test_a_card_can_be_added_to_an_existing_collection() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 1);
        $this->createCollectionCard($uuid, 1, '', 4);
        $this->createCollectionCard($uuid, 2, '', 5);
        $collection      = Collection::uuid($uuid);
        $collectionCards = $collection->cards;

        $this->assertEquals(10, $collectionCards->sum('pivot.quantity'));
        $this->assertEquals(3, $collectionCards->count());
    }

    public function test_a_card_with_a_negative_quantity_cannot_be_added_to_a_collection() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', -3);
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();

        $this->assertNull($collectionCard);
    }

    public function test_a_collection_card_can_be_removed() : void
    {
        // set user
        $this->act();

        // get collection card
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid, 0, '', 10);

        // get model
        $collection         = Collection::uuid($collectionUuid);
        $collectionCards    = $collection->cardSummaries;
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(10, $quantity);

        // delete cards
        $this->deleteCards($collectionUuid, $collectionCards->toArray());

        // update collection
        $collection->refresh();
        $collectionCards    = $collection->cardSummaries;
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(0, $quantity);
    }

    public function test_a_collection_card_can_decrease_in_quantity() : void
    {
        // set user
        $this->act();

        // get collection card
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid, 0, '', 10);

        // get model
        $collection         = Collection::uuid($collectionUuid);
        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(10, $quantity);

        // remove quantity
        $this->updateCard($card->toArray(), [
            'change'        => -3,
            'oldPrice'      => $card->current_price,
            'newPrice'      => $card->current_price,
            'oldCondition'  => $card->condition,
            'newCondition'  => $card->condition,
        ]);

        // get state
        $collection->refresh();
        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $count              = $collectionCards->count();
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(1, $count);
        $this->assertEquals(7, $quantity);
    }

    public function test_a_collection_card_can_decrement_quantity() : void
    {
        // set user
        $this->act();

        // get collection card
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid, 5, 'foil', 1, 'NM');
        $this->createCollectionCard($collectionUuid, 5, 'foil', 1, 'NM');
        $this->createCollectionCard($collectionUuid, 5, 'foil', 1, 'NM');

        // get model
        $collection         = Collection::uuid($collectionUuid);
        $collectionCards    = $collection->cardSummaries;
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(3, $quantity);
        $this->assertCount(1, $collectionCards);

        // decrement quantity by one
        $this->createCollectionCard($collectionUuid, 5, 'foil', -1, 'NM');

        // refresh state
        $collection->refresh();
        $collectionCards    = $collection->cardSummaries;
        $quantity           = $collectionCards->sum('quantity');
        $card               = $collectionCards->first();

        // assertions
        $this->assertEquals(2, $quantity);
        $this->assertCount(1, $collectionCards);
        $this->assertCount(4, $collection->cards);

        // remove quantity
        $this->updateCard($card->toArray(), [
            'change'        => -2,
            'oldPrice'      => $card->current_price,
            'newPrice'      => $card->current_price,
            'oldCondition'  => $card->condition,
            'newCondition'  => $card->condition,
        ]);

        // get state
        $collection->refresh();
        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $count              = $collectionCards->count();
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(1, $count);
        $this->assertEquals(0, $quantity);
    }

    public function test_a_collection_card_can_increment_quantity() : void
    {
        // set user
        $this->act();

        // get collection card
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid, 5, 'foil', 1, 'NM');

        // // get model
        $collection         = Collection::uuid($collectionUuid);
        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $quantity           = $collectionCards->sum('quantity');

        // // assertions
        $this->assertEquals(1, $quantity);
        $this->assertCount(1, $collectionCards);

        // increment quantity
        $this->createCollectionCard($collectionUuid, 5, 'foil', 1, 'NM');

        // get state
        $collection->refresh();

        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $count              = $collectionCards->count();
        $quantity           = $collectionCards->sum('quantity');
        $first              = $collectionCards->first();
        $second             = $collectionCards->find(2);

        // assertions
        $this->assertEquals(1, $count);
        $this->assertEquals(2, $quantity);
        $this->assertEquals('foil', $first['finish']);
        $this->assertNull($second);
    }

    public function test_a_collection_card_cannot_decrease_in_quantity_beyond_zero()
    {
        // set user
        $this->act();

        // get collection card
        $collectionUuid = $this->createCollection();
        $this->createCollectionCard($collectionUuid, 0, '', 10);

        // get model
        $collection         = Collection::uuid($collectionUuid);
        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(10, $quantity);

        // remove quantity
        $this->updateCard($card->toArray(), [
            'change'        => -15,
            'oldPrice'      => $card->current_price,
            'newPrice'      => $card->current_price,
            'oldCondition'  => $card->condition,
            'newCondition'  => $card->condition,
        ]);

        // get state
        $collection->refresh();
        $collectionCards    = $collection->cardSummaries;
        $card               = $collectionCards->first();
        $count              = $collectionCards->count();
        $quantity           = $collectionCards->sum('quantity');

        // assertions
        $this->assertEquals(1, $count);
        $this->assertEquals(10, $quantity);
    }

    public function test_a_collection_card_summary_is_created_when_a_card_is_added() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $cardSummary    = $collection->cardSummaries->first();

        $this->assertNotNull($cardSummary);
        $this->assertEquals(2, $cardSummary->quantity);
        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertGreaterThan(0, $cardPrice);
    }

    public function test_adding_different_cards_creates_new_collection_card_summaries() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $cardSummary    = $collection->cardSummaries->first();

        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(2, $cardSummary->quantity);

        $this->createCollectionCard($uuid, 1, '', 3);

        $collection->refresh();

        $this->assertEquals(2, $collection->cards->count());

        $collectionCard     = $collection->cards->get(1);
        $cardPrice          = $collectionCard->pivot->price_when_added;

        $this->assertEquals(2, $collection->cardSummaries->count());
        $cardSummary  = $collection->cardSummaries->last();

        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(3, $cardSummary->quantity);
    }

    public function test_adding_the_same_card_twice_does_not_create_a_new_collection_card_summary() : void
    {
        $this->act();
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $cardSummary    = $collection->cardSummaries->first();

        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(2, $cardSummary->quantity);

        $this->createCollectionCard($uuid, 0, '', 3);

        $collection->refresh();

        $this->assertEquals(2, $collection->cards->count());

        $collectionCard     = $collection->cards->get(1);
        $cardPrice          = $collectionCard->pivot->price_when_added;

        $this->assertEquals(1, $collection->cardSummaries->count());
        $cardSummary  = $collection->cardSummaries->first();

        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(5, $cardSummary->quantity);
    }
}
