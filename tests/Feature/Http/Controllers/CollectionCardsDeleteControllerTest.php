<?php

namespace Tests\Feature\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use App\Domain\Collections\Models\Collection;
use Tests\Feature\Domain\CardCollectionTestCase;

/**
 * @see \App\Http\Controllers\CollectionCardsDeleteController
 */
class CollectionCardsDeleteControllerTest extends CardCollectionTestCase
{
    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->act();

        $collection     = $this->createCollection();
        $requestCards   = [
            $this->createCollectionCardRequest($collection, 0),
            $this->createCollectionCardRequest($collection, 1),
            $this->createCollectionCardRequest($collection, 2),
        ];

        foreach ($requestCards as $requestCard) {
            (new UpdateCollectionCard)($requestCard);
        }

        $collectionModel    = Collection::uuid($collection);
        $cardSummaries      = $collectionModel
            ->cardSummaries
            ->where('quantity', '>', 0);

        $this->assertCount(3, $cardSummaries);

        $response = $this->post(route('collection-cards-delete.update', ['collection' => $collection]), [
            'collection'    => $collection,
            'items'         => $cardSummaries->toArray(),
        ]);

        $response->assertOk();
        $collectionModel->refresh();
        $cardSummaries      = $collectionModel
            ->cardSummaries
            ->where('quantity', '>', 0);

        $this->assertCount(0, $cardSummaries);
    }
}
