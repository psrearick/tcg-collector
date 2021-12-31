<?php

namespace App\Domain\Collections\Aggregate\Queries;

use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\EventQuery;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class CollectionCardsSummary extends EventQuery
{
    private array $collectionCards = [];

    public function __construct(string $collection)
    {
        EloquentStoredEvent::query()
            ->where('event_class', '=', CollectionCardUpdated::class)
            ->where('aggregate_uuid', '=', $collection)
            ->cursor()
            ->each(fn (EloquentStoredEvent $event) => $this->apply($event->toStoredEvent()));
    }

    public function cards() : array
    {
        return $this->collectionCards;
    }

    protected function applyCollectionCardUpdated(CollectionCardUpdated $collectionCardUpdated) : void
    {
        $attributes = $collectionCardUpdated->collectionCardAttributes;
        $cardUuid   = $attributes['updated']['uuid'];
        $finish     = $attributes['updated']['finish'];
        $change     = $attributes['quantity_diff'];

        if (!isset($this->collectionCards[$cardUuid])) {
            $this->collectionCards[$cardUuid] = [$finish => $change];

            return;
        }

        if (!isset($this->collectionCards[$cardUuid][$finish])) {
            $this->collectionCards[$cardUuid][$finish] = $change;

            return;
        }

        $current                                   = $this->collectionCards[$cardUuid][$finish];
        $this->collectionCards[$cardUuid][$finish] = $current + $change;
    }
}
