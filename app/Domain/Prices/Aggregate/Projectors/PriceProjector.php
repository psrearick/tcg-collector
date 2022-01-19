<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Prices\Aggregate\Events\PriceCreated;
use App\Domain\Prices\Models\Price;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PriceProjector extends Projector
{
    public function onPriceCreated(PriceCreated $priceCreated) : void
    {
        $attributes = $priceCreated->priceAttributes;

        Price::create([
            'card_uuid'     => $attributes['card_uuid'],
            'provider_uuid' => $attributes['provider_uuid'],
            'price'         => $attributes['price'],
            'type'          => $attributes['type'],
        ]);
    }
}
