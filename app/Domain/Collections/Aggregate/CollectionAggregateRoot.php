<?php

namespace App\Domain\Collections\Aggregate;

use App\Domain\Collections\Aggregate\Events\CollectionCardsMoved;
use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Aggregate\Events\CollectionUpdated;
use Illuminate\Support\Facades\Auth;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CollectionAggregateRoot extends AggregateRoot
{
    public function createCollection(array $attributes) : self
    {
        $attributes['user_id'] = $attributes['user_id'] ?? Auth::id();
        $attributes['uuid']    = $this->uuid();
        $this->recordThat(new CollectionCreated($attributes));

        return $this;
    }

    public function deleteCollection() : self
    {
        $this->recordThat(new CollectionDeleted());

        return $this;
    }

    public function moveCollection(string $uuid, string $destination, int $userId) : self
    {
        $this->recordThat(new CollectionMoved($uuid, $destination, $userId));

        return $this;
    }

    public function moveCollectionCards(string $uuid, string $destination, array $cards) : self
    {
        $this->recordThat(new CollectionCardsMoved($uuid, $destination, $cards));

        return $this;
    }

    public function updateCollection(array $attributes) : self
    {
        $this->recordThat(new CollectionUpdated($attributes));

        return $this;
    }

    public function updateCollectionCard(array $attributes) : self
    {
        $this->recordThat(new CollectionCardUpdated($attributes));

        return $this;
    }
}
