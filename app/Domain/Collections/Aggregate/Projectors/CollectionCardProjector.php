<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardsDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionCardsMoved;
use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Aggregate\Projectors\Actions\CreateCollectionCard;
use App\Domain\Collections\Aggregate\Projectors\Traits\UpdatesCollectionCards;
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

    use UpdatesCollectionCards;

    private CreateCollectionCard $createCollectionCard;

    public function __construct()
    {
        $this->createCollectionCard = new CreateCollectionCard;
    }

    public function onCollectionCardsDeleted(CollectionCardsDeleted $event) : void
    {
    }

    public function onCollectionCardsMoved(CollectionCardsMoved $event) : void
    {
    }

    public function onCollectionCardUpdated(CollectionCardUpdated $event) : void
    {
        $attributes = $event->collectionCardAttributes;

        if (!$this->isValidUpdate($attributes)) {
            Cache::restoreLock('saving-collection-card', $attributes['lock'])->release();

            return;
        }

        ($this->createCollectionCard)($attributes);

        if ($this->shouldUpdateCollectionCardSummary($attributes)) {
            $this->updateCollectionCardSummary($attributes);
        } else {
            $this->createCollectionCardSummary($attributes);
        }

        Cache::restoreLock('saving-collection-card', $attributes['lock'])->release();
    }
}
