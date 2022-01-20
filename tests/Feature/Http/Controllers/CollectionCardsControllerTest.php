<?php

namespace Tests\Feature\Http\Controllers;

use App\Domain\Collections\Models\Collection;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Domain\CardCollectionTestCase;

/**
 * @see \App\Http\Controllers\CollectionCardsController
 */
class CollectionCardsControllerTest extends CardCollectionTestCase
{
    public function invalidChanges() : array
    {
        return [
            ['id', 'invalid'],
            ['finish', 'invalid'],
            ['change', -1],
        ];
    }

    /**
     * @test
     * @dataProvider invalidChanges
     */
    public function test_store_requires_valid_change(string $key, mixed $value) : void
    {
        $this->act();

        $collection     = $this->createCollection();
        $cardRequest    = $this->createCollectionCardRequest($collection);
        $change         = $cardRequest['change'];
        $change[$key]   = $value;
        $response       = $this->post(route('collection-cards.store', ['collection' => $collection]), $change);
        $state          = $this->getState(null, Collection::uuid($collection));

        $response->assertStatus(500);
        $this->assertEmpty($state['collection']);
    }

    public function test_store_returns_a_redirect_response() : void
    {
        $this->act();

        $collection     = $this->createCollection();
        $cardRequest    = $this->createCollectionCardRequest($collection);
        $change         = $cardRequest['change'];
        $response       = $this->post(route('collection-cards.store', ['collection' => $collection]), $change);
        $state          = $this->getState(null, Collection::uuid($collection));

        $response->assertRedirect();

        $this->assertEquals(1, $state['collection']['total_cards']);
    }

    public function test_store_returns_an_ok_response() : void
    {
        $this->act();

        $collection     = $this->createCollection();
        $cardRequest    = $this->createCollectionCardRequest($collection);
        $change         = $cardRequest['change'];
        $response       = $this->postJson(route('collection-cards.store', ['collection' => $collection]), $change);

        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('uuid', $change['id'])
                    ->where('quantities', [
                        $change['finish'] => 1,
                    ])
                    ->etc()
        );
    }
}
