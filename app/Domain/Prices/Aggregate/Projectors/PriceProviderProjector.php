<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Prices\Aggregate\Events\PriceProviderCreated;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PriceProviderProjector extends Projector
{
    public function onPriceProviderCreated(PriceProviderCreated $priceProviderCreated) : void
    {
        Log::alert($priceProviderCreated->priceProviderAttributes);
    }
}