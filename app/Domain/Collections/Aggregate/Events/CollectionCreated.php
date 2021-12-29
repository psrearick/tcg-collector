<?php

namespace App\Domain\Collections\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CollectionCreated extends ShouldBeStored
{
        /** @var array */
        public $collectionAttributes;

        public function __construct(array $collectionAttributes)
        {
            $this->collectionAttributes = $collectionAttributes;
        }
}