<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Models\CollectionCardSummary;

class GetCollectionCards
{
    public function __invoke(string $uuid)
    {
        $collectionCards = CollectionCardSummary::with('card', 'card.frameEffects', 'card.set')
            ->where('collection_uuid', '=', $uuid)
            ->where('quantity', '>', 0);

        return $collectionCards;
    }
}
