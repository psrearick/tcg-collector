<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Domain\Prices\Aggregate\Events\PriceCreated;
use Illuminate\Contracts\Cache\LockTimeoutException;
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

        // NOTE:
        // adding this will update each collection summary every time a price
        // is added. This makes adding prices too slow. Instead, we update the
        // collections in bulk, once, using a job after the prices have updated.
        // if we update prices dynamically throughout the day, we will need to find
        // a way to update summaries as well when the price is updated from
        // tcgplayer, but not on each night's bulk update

        // $summaries->each(function (CollectionCardSummary $summary, $index) use ($from) {
        //     $previous = $from[$index];
        //     $this->updateCard($summary, $previous);
        // });
    }

    /**
     * @throws LockTimeoutException
     */
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
