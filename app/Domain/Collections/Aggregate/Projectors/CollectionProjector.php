<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardsDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionCardsMoved;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Aggregate\Events\CollectionUpdated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\UpdateCollectionAncestryTotals;
use App\Domain\Prices\Aggregate\Actions\UpdateFolderAncestryTotals;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionProjector extends Projector
{
    public function onCollectionCardsDeleted(CollectionCardsDeleted $event) : void
    {
        $cards          = $event->cards;
        $collectionUuid = $event->uuid;
        $collection     = Collection::uuid($collectionUuid);

        foreach ($cards as $card) {
            $collection->cards()
                ->where('uuid', '=', $card['uuid'])
                ->wherePivot('finish', '=', $card['finish'])
                ->detach();

            CollectionCardSummary::where('collection_uuid', '=', $collectionUuid)
                ->where('card_uuid', '=', $card['uuid'])
                ->where('finish', '=', $card['finish'])
                ->delete();
        }

        (new UpdateCollectionAncestryTotals)($collection);
    }

    public function onCollectionCardsMoved(CollectionCardsMoved $event) : void
    {
        $cards           = $event->cards;
        $originUuid      = $event->uuid;
        $origin          = Collection::uuid($originUuid);
        $destinationUuid = $event->destination;
        $destination     = Collection::uuid($destinationUuid);

        foreach ($cards as $card) {

            // Update pivot values
            $collectionCards = $origin->cards()
                ->where('uuid', '=', $card['uuid'])
                ->wherePivot('finish', '=', $card['finish'])
                ->get();

            foreach ($collectionCards as $collectionCard) {
                $collectionCard->collections()
                    ->updateExistingPivot($origin, ['collection_uuid' => $destinationUuid], true);
            }

            // move card summary data
            $originCard = CollectionCardSummary::where('collection_uuid', '=', $originUuid)
                ->where('card_uuid', '=', $card['uuid'])
                ->where('finish', '=', $card['finish'])
                ->first();

            $destinationCard = CollectionCardSummary::where('collection_uuid', '=', $destinationUuid)
                ->where('card_uuid', '=', $card['uuid'])
                ->where('finish', '=', $card['finish'])
                ->first();

            if ($destinationCard) {
                $destinationCard->update([
                    'quantity' => $destinationCard->quantity + $card['quantity'],
                ]);

                $originCard->delete();
            } else {
                $originCard->update([
                    'collection_uuid' => $destinationUuid,
                ]);
            }
        }

        $destinationTotals = (new GetCollectionTotals)($destination);
        $destination->summary->update($destinationTotals);
        $destinationFolder = $destination->folder;
        if ($destinationFolder) {
            (new UpdateFolderAncestryTotals)($destinationFolder);
        }

        $originTotals = (new GetCollectionTotals)($origin);
        $origin->summary->update($originTotals);
        $originFolder = $origin->folder;
        if ($originFolder) {
            (new UpdateFolderAncestryTotals)($originFolder);
        }
    }

    public function onCollectionCreated(CollectionCreated $event) : void
    {
        $attributes = $event->collectionAttributes;
        $collection = Collection::create([
            'uuid'          => $attributes['uuid'],
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'is_public'     => $attributes['is_public'],
            'user_id'       => $attributes['user_id'],
            'folder_uuid'   => $attributes['folder_uuid'],
        ]);

        if ($attributes['groups']) {
            $collection->groups()->sync($attributes['groups']);
        }
    }

    public function onCollectionDeleted(CollectionDeleted $event) : void
    {
        $collection = Collection::uuid($event->aggregateRootUuid());
        $folderUuid = $collection->folder_uuid;
        $collection->delete();

        if ($folderUuid) {
            $folder = Folder::uuid($folderUuid);
            if ($folder) {
                (new UpdateFolderAncestryTotals)($folder);
            }
        }
    }

    public function onCollectionMoved(CollectionMoved $event) : void
    {
        $collection         = Collection::uuid($event->uuid);
        $destinationUuid    = $event->destination;
        $originalParentUuid = $collection->folder_uuid;

        $collection->update([
            'folder_uuid' => $destinationUuid,
        ]);

        if ($destinationUuid) {
            (new UpdateFolderAncestryTotals)(Folder::uuid($destinationUuid));
        }

        if ($originalParentUuid) {
            (new UpdateFolderAncestryTotals)(Folder::uuid($originalParentUuid));
        }
    }

    public function onCollectionUpdated(CollectionUpdated $event) : void
    {
        $attributes = $event->collectionAttributes;
        $collection = Collection::uuid($attributes['uuid']);
        $collection->update([
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'is_public'     => $attributes['is_public'],
        ]);

        if ($attributes['groups']) {
            $collection->groups()->sync($attributes['groups']);
        }
    }
}
