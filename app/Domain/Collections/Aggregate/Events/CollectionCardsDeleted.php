<?php

namespace App\Domain\Collections\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CollectionCardsDeleted extends ShouldBeStored
{
    /** @var array */
    public $cards;

    /** @var string */
    public $uuid;

    public function __construct(string $uuid, array $cards)
    {
        $this->uuid         = $uuid;
        $this->cards        = $cards;
    }
}
