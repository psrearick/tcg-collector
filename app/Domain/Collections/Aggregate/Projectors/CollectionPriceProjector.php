<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Domain\Prices\Aggregate\Events\PriceCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionPriceProjector extends Projector
{
    public function onPriceCreated(PriceCreated $priceCreated) : void
    {
        $attributes = $priceCreated->priceAttributes;
        $summaries  = CollectionCardSummary::where('card_uuid', '=', $attributes['card_uuid'])
            ->where('finish', '=', (new MatchType)($attributes['type']));
        $from       = $summaries->get();
        $summaries->update([
            'current_price' => $attributes['price'],
        ]);

        $summaries->each(function (CollectionCardSummary $summary, $index) use ($from) {
            $previous = $from[$index];
            $this->updateCard($summary, $previous);
        });
    }

    public function updateCard(CollectionCardSummary $summary, CollectionCardSummary $previous) : void
    {
        $data = [
            'acquired_price'    => $summary->price_when_added,
            'change'            => 0,
            'condition'         => $summary->condition,
            'finish'            => $summary->finish,
            'id'                => $summary->card_uuid,
            'price'             => $summary->current_price,
            'quantity'          => $summary->quantity,
            'from'              => [
                'condition'         => $summary->condition,
                'finish'            => $summary->finish,
                'acquired_price'    => $previous->price_when_added,
                'price'             => $previous->current_price,
            ],
        ];

        (new UpdateCollectionCard)([
            'uuid'      => $summary->collection_uuid,
            'change'    => $data,
        ]);
    }
}
