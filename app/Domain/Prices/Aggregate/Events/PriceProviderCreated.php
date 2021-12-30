<?php

namespace App\Domain\Prices\Aggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PriceProviderCreated extends ShouldBeStored
{
    /** @var array */
    public $priceProviderAttributes;

    public function __construct(array $priceProviderAttributes)
    {
        $this->priceProviderAttributes = $priceProviderAttributes;
    }
}
