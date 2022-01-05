<?php

namespace App\Domain\Prices\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PriceCreated extends ShouldBeStored
{
    /** @var array */
    public $priceAttributes;

    public function __construct(array $priceAttributes)
    {
        $this->priceAttributes = $priceAttributes;
    }
}
