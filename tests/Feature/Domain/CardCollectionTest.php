<?php

namespace Tests\Feature\Domain;

use App\Domain\Cards\Models\Card;
use Database\Seeders\CardsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use App\Domain\Collections\Models\Collection;

class CardCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CardsSeeder::class);
    }

    public function test_a_card_can_be_added_to_a_collection() : void
    {
        $uuid = $this->createCollection();
        $card = Card::first();
        $data = [
            'uuid'      => $uuid,
            'change'    => [
                'id'        => $card->uuid,
                'finish'    => $card->finishes->first()->name,
                'change'    => 2,
            ],
        ];

        $changedCard    = (new UpdateCollectionCard)($data);
        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $cardQuantites  = $changedCard['quantities'][$data['change']['finish']];

        $this->assertEquals(2, $collectionCard->pivot->quantity);
        $this->assertEquals(2, $cardQuantites);
        $this->assertEquals(1, $collection->cards->count());
    }

    public function test_a_collections_value_is_updated_when_a_card_is_added() : void
    {
        $uuid = $this->createCollection();
        $card = Card::first();
        $data = [
            'uuid'      => $uuid,
            'change'    => [
                'id'        => $card->uuid,
                'finish'    => $card->finishes->first()->name,
                'change'    => 2,
            ],
        ];

        (new UpdateCollectionCard)($data);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $summary        = $collection->summary;
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $totalPrice     = $cardPrice * 2;

        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(2, $summary->total_cards);

        $cardSummary  = $collection->cardSummaries->first();
        
        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(2, $cardSummary->quantity);

        $card = Card::all()->get(2);
        $data = [
            'uuid'      => $uuid,
            'change'    => [
                'id'        => $card->uuid,
                'finish'    => $card->finishes->first()->name,
                'change'    => 3,
            ],
        ];
        (new UpdateCollectionCard)($data);

        $collection->refresh();

        $collectionCard     = $collection->cards->get(1);
        $summary            = $collection->summary;
        $cardPrice          = $collectionCard->pivot->price_when_added;
        $newCardTotal       = $cardPrice * 3;
        $collectionPrice    = $totalPrice + $newCardTotal;


        $this->assertEquals($collectionPrice, $summary->current_value);
        $this->assertEquals(5, $summary->total_cards);

        $cardSummary  = $collection->cardSummaries->get(1);
        
        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(3, $cardSummary->quantity);
    }

    public function test_adding_the_same_card_twice_does_not_create_a_new_summary() : void
    {
        $uuid = $this->createCollection();
        $card = Card::first();
        $data = [
            'uuid'      => $uuid,
            'change'    => [
                'id'        => $card->uuid,
                'finish'    => $card->finishes->first()->name,
                'change'    => 2,
            ],
        ];

        (new UpdateCollectionCard)($data);

        $collection     = Collection::uuid($uuid);
        $collectionCard = $collection->cards->first();
        $summary        = $collection->summary;
        $cardPrice      = $collectionCard->pivot->price_when_added;
        $totalPrice     = $cardPrice * 2;

        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(2, $summary->total_cards);

        $cardSummary  = $collection->cardSummaries->first();
        
        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(2, $cardSummary->quantity);

        $data = [
            'uuid'      => $uuid,
            'change'    => [
                'id'        => $card->uuid,
                'finish'    => $card->finishes->first()->name,
                'change'    => 3,
            ],
        ];
        (new UpdateCollectionCard)($data);

        $collection->refresh();

        $this->assertEquals(2, $collection->cards->count());

        $collectionCard     = $collection->cards->get(1);
        $summary            = $collection->summary;
        $cardPrice          = $collectionCard->pivot->price_when_added;
        $newCardTotal       = $cardPrice * 3;
        $collectionPrice    = $totalPrice + $newCardTotal;

        $this->assertEquals($collectionPrice, $summary->current_value);
        $this->assertEquals(5, $summary->total_cards);

        $this->assertEquals(1, $collection->cardSummaries->count());
        $cardSummary  = $collection->cardSummaries->first();
        
        $this->assertEquals($cardPrice, $cardSummary->price_when_added);
        $this->assertEquals(5, $cardSummary->quantity);
    }

    private function createCollection() : string
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Collection',
            'name'          => 'description 01',
            'is_public'     => false,
        ];

        return (new CreateCollection)($params);
    }
}
