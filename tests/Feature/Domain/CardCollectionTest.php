<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Models\Collection;
use App\Models\User;

class CardCollectionTest extends CardCollectionTestCase
{
    public function test_a_card_can_be_added_to_a_new_collection() : void
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 2);
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();

        $this->assertEquals(2, $collectionCard->pivot->quantity);
        $this->assertEquals(1, $collection->cards->count());
    }

    public function test_a_card_can_be_added_to_an_existing_collection() : void
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', 1);
        $this->createCollectionCard($uuid, 1, '', 4);
        $this->createCollectionCard($uuid, 2, '', 5);
        $collection      = Collection::uuid($uuid);
        $collectionCards = $collection->cards;

        $this->assertEquals(10, $collectionCards->sum('pivot.quantity'));
        $this->assertEquals(3, $collectionCards->count());
    }

    public function test_a_card_with_a_negative_cannot_be_added_to_a_collection() : void
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $uuid = $this->createCollection();
        $this->createCollectionCard($uuid, 0, '', -3);
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();

        $this->assertNull($collectionCard);
    }

    public function test_a_collection_card_summary_is_created_when_a_card_is_added() : void
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
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
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
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
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
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

    // updates folder summary
    // not negative
    // decreasing quantities
    // removing cards
}
