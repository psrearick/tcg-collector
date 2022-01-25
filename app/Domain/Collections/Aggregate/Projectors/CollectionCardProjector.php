<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardsDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionCardsMoved;
use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Aggregate\Projectors\Services\UpdateCollectionCardsService;
use App\Domain\Collections\Models\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionCardProjector extends Projector
{
    // Events
    //      CollectionCardsDeleted
    //      CollectionCardsMoved
    //      CollectionCardUpdated

    // Triggers
    //      Created
    //          First Addition
    //          Variant
    //      Moved
    //      Acquired Price Changed
    //      New Price Added
    //      Quantity Changed
    //          Deleted
    //          Merged
    //          Added
    //          Removed

    // Effects
    //      Add new record to card_collections table
    //      Update collection_card_summaries table
    //          Create record
    //          Update record
    //          Delete record
    //      Update summaries table
    //          Folder
    //          Collection

    public function onCollectionCardsDeleted(CollectionCardsDeleted $event) : void
    {
        $cards          = $event->cards;

        foreach ($cards as $card) {
            $service = (new UpdateCollectionCardsService)
                ->setCollectionCardFromPivot($card);
            $service->removeCollectionCardWithEvent();
        }
    }

    public function onCollectionCardsMoved(CollectionCardsMoved $event) : void
    {
        $cards           = $event->cards;
        $originUuid      = $event->uuid;
        $destinationUuid = $event->destination;
        $origin          = Collection::uuid($originUuid);
        $destination     = Collection::uuid($destinationUuid);

        foreach ($cards as $card) {
            $removeCollectionCard = (new UpdateCollectionCardsService)
                ->setCollectionCardFromPivot($card)
                ->removeCollectionCardWithEvent();

            $destinationCard = (new UpdateCollectionCardsService)
                ->setCollectionCardFromPivot($card)
                ->setAttribute('collection', $destinationUuid)
                ->createCollectionCardWithEvent();
        }
    }

    public function onCollectionCardUpdated(CollectionCardUpdated $event) : void
    {
        $attributes = $event->collectionCardAttributes;

        $service = (new UpdateCollectionCardsService)
            ->setCollectionCardFromEventAttributes($attributes);

        if (!$service->isValidUpdate()) {
            Cache::restoreLock('saving-collection-card', $attributes['lock'])->release();

            return;
        }

        $service
            ->createCollectionCard()
            ->setCollectionCardSummaryFromEventAttributes($attributes);

        if ($service->shouldUpdateCollectionCardSummary()) {
            $service->updateCollectionCardSummary($attributes);
        } else {
            $service->createCollectionCardSummary($attributes);
        }

        Cache::restoreLock('saving-collection-card', $attributes['lock'])->release();
    }
}
