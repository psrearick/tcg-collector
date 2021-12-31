<?php

namespace App\Domain\Collections\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CollectionCardUpdated extends ShouldBeStored
{
    /** @var array */
    public $collectionCardAttributes;

    public function __construct(array $collectionCardAttributes)
    {
        $this->collectionCardAttributes = $collectionCardAttributes;
    }
}
