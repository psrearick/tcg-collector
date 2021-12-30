<?php

namespace App\Domain\Collections\Aggregate;

use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use Illuminate\Support\Facades\Auth;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CollectionAggregateRoot extends AggregateRoot
{
    public function createCollection(array $attributes) : self
    {
        $attributes['user_id'] = Auth::id();
        $attributes['uuid']    = $this->uuid();
        $this->recordThat(new CollectionCreated($attributes));

        return $this;
    }

    public function deleteCollection()
    {
        $this->recordThat(new CollectionDeleted());

        return $this;
    }

    public function moveCollection(string $uuid, string $destination, int $userId) : self
    {
        $this->recordThat(new CollectionMoved($uuid, $destination, $userId));

        return $this;
    }
}
