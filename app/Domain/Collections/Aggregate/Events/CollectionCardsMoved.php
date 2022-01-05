<?php

namespace App\Domain\Collections\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CollectionCardsMoved extends ShouldBeStored
{
    /** @var array */
    public $cards;

    /** @var string */
    public $destination;

    /** @var string */
    public $uuid;

    public function __construct(string $uuid, string $destination, array $cards)
    {
        $this->uuid         = $uuid;
        $this->destination  = $destination;
        $this->cards        = $cards;
    }
}
