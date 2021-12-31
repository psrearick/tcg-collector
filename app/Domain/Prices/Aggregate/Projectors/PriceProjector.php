<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Prices\Aggregate\Events\PriceCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PriceProjector extends Projector
{
    public function onPriceProviderCreated(PriceCreated $priceCreated) : void
    {
    }
}
