<?php

namespace Tests\Feature\Http\Controllers;

use Tests\Feature\Domain\CardCollectionTestCase;
use Tests\Feature\Domain\CollectionTestData;

/**
 * @see \App\Http\Controllers\CollectionCardsMoveController
 */
class CollectionCardsMoveControllerTest extends CardCollectionTestCase
{
    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->act();

        $root = new CollectionTestData();
        $root->init()->addCollections(2)->addCards(3);

        $collection     = $root->getCollection();
        $destination    = $root->getCollection(1);

        $this->assertCount(3, $collection->cardSummaries);
        $this->assertCount(0, $destination->cardSummaries);

        $response = $this->post(route('collection-cards-move.update', ['collection' => $collection->uuid]), [
            'collection'    => $destination->uuid,
            'items'         => $collection->cardSummaries->toArray(),
        ]);

        $response->assertOk();
        $root->refresh();

        $this->assertCount(0, $this->withQuantity($collection->cardSummaries));
        $this->assertCount(3, $this->withQuantity($destination->cardSummaries));
    }
}
