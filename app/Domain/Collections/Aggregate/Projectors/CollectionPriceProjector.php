<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Domain\Prices\Aggregate\Events\PriceCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionPriceProjector extends Projector
{
    public function onPriceCreated(PriceCreated $priceCreated) : void
    {
        $attributes = $priceCreated->priceAttributes;

        CollectionCardSummary::where('card_uuid', '=', $attributes['card_uuid'])
            ->where('finish', '=', (new MatchType)($attributes['type']))
            ->update([
                'current_price' => $attributes['price'],
            ]);
    }
}
