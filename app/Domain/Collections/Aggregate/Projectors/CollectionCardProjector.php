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
        // $collectionUuid = $event->uuid;
        // $collection     = Collection::uuid($collectionUuid);

        foreach ($cards as $card) {
            $service = (new UpdateCollectionCardsService)
                ->setCollectionCardFromPivot($card);
            $service->removeCollectionCard();
            //     $collection->cards()
        //         ->where('uuid', '=', $card['uuid'])
        //         ->wherePivot('finish', '=', $card['finish'])
        //         ->detach();

        //     CollectionCardSummary::where('collection_uuid', '=', $collectionUuid)
        //         ->where('card_uuid', '=', $card['uuid'])
        //         ->where('finish', '=', $card['finish'])
        //         ->delete();
        }

        // (new UpdateCollectionAncestryTotals)($collection);
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

            //     // Update pivot values
        //     $collectionCards = $origin->cards()
        //         ->where('uuid', '=', $card['uuid'])
        //         ->wherePivot('finish', '=', $card['finish'])
        //         ->get();

        //     foreach ($collectionCards as $collectionCard) {
        //         $collectionCard->collections()
        //             ->updateExistingPivot($origin, ['collection_uuid' => $destinationUuid], true);
        //     }

        //     // move card summary data
        //     $originCard = CollectionCardSummary::where('collection_uuid', '=', $originUuid)
        //         ->where('card_uuid', '=', $card['uuid'])
        //         ->where('finish', '=', $card['finish'])
        //         ->first();

        //     $destinationCard = CollectionCardSummary::where('collection_uuid', '=', $destinationUuid)
        //         ->where('card_uuid', '=', $card['uuid'])
        //         ->where('finish', '=', $card['finish'])
        //         ->first();

        //     if ($destinationCard) {
        //         $destinationCard->update([
        //             'quantity' => $destinationCard->quantity + $card['quantity'],
        //         ]);

        //         $originCard->delete();
        //     } else {
        //         $originCard->update([
        //             'collection_uuid' => $destinationUuid,
        //         ]);
        //     }
        }

        // $destinationTotals = (new GetCollectionTotals)($destination);
        // $destination->summary->update($destinationTotals);
        // $destinationFolder = $destination->folder;
        // if ($destinationFolder) {
        //     (new UpdateFolderAncestryTotals)($destinationFolder);
        // }

        // $originTotals = (new GetCollectionTotals)($origin);
        // $origin->summary->update($originTotals);
        // $originFolder = $origin->folder;
        // if ($originFolder) {
        //     (new UpdateFolderAncestryTotals)($originFolder);
        // }
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
