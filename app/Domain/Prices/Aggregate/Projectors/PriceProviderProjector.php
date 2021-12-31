<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Prices\Aggregate\Events\PriceProviderCreated;
use App\Domain\Prices\Models\PriceProvider;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PriceProviderProjector extends Projector
{
    public function onPriceProviderCreated(PriceProviderCreated $priceProviderCreated) : void
    {
        $attributes = $priceProviderCreated->priceProviderAttributes;

        PriceProvider::firstOrCreate([
            'name'  => $attributes['name'],
        ], [
            'uuid'  => $attributes['uuid'],
        ]);
    }
}
