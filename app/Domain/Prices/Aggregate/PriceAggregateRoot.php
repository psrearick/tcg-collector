<?php

namespace App\Domain\Prices\Aggregate;

use App\Domain\Prices\Aggregate\Events\PriceCreated;
use App\Domain\Prices\Aggregate\Events\PriceProviderCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class PriceAggregateRoot extends AggregateRoot
{
    public function createPrice(array $attributes) : self
    {
        $attributes['uuid'] = $this->uuid();
        $this->recordThat(new PriceCreated($attributes));

        return $this;
    }

    public function createPriceProvider(array $attributes) : self
    {
        $attributes['uuid'] = $this->uuid();
        $this->recordThat(new PriceProviderCreated($attributes));

        return $this;
    }
}
