<?php

namespace App\Domain\Collections\Aggregate;

use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Illuminate\Support\Facades\Auth;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;

class CollectionAggregateRoot extends AggregateRoot
{
    public function createCollection(array $attributes)
    {
        $attributes['user_id'] = Auth::id();
        $attributes['uuid'] = $this->uuid();
        $this->recordThat(new CollectionCreated($attributes));
        
        return $this;
    }

    public function deleteCollection()
    {
        $this->recordThat(new CollectionDeleted());
        
        return $this;
    }
}
