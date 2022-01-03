<?php

namespace App\Domain\Collections\Aggregate\Queries;

use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use App\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\EventQuery;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class CollectionCardsSummary extends EventQuery
{
    private array $collectionCards = [];

    private array $collectionCardsList = [];

    public function __construct(string $collection)
    {
        EloquentStoredEvent::query()
            ->where('event_class', '=', CollectionCardUpdated::class)
            ->where('aggregate_uuid', '=', $collection)
            ->cursor()
            ->each(fn (EloquentStoredEvent $event) => $this->apply($event->toStoredEvent()));
    }

    public function cards() : array
    {
        return $this->collectionCards;
    }

    public function list() : SupportCollection
    {
        $cards = [];
        foreach ($this->collectionCardsList as $uuid => $card) {
            foreach ($card as $data) {
                if ($data['quantity'] < 1) {
                    continue;
                }
                $cards[] = $data;
            }
        }

        $collection = (new SupportCollection($cards));
        $prices     = (new GetLatestPrices)($collection->pluck('uuid')->toArray());
        $collection = $collection->map(function ($card) use ($prices) {
            $type = (new MatchFinish)($card['finish']);
            $price = $prices
                ->where('type', '=', $type)
                ->where('card_uuid', '=', $card['uuid'])
                ->first();
            $card['price']              = $price->price;
            $card['name_normalized']    = $price->name_normalized;
            $card['set_id']             = $price->set_id;

            return $card;
        });

        return $collection;
    }

    protected function applyCollectionCardUpdated(CollectionCardUpdated $collectionCardUpdated) : void
    {
        $attributes = $collectionCardUpdated->collectionCardAttributes;
        $cardUuid   = $attributes['updated']['uuid'];
        $finish     = $attributes['updated']['finish'];
        $change     = $attributes['quantity_diff'];

        if (!isset($this->collectionCards[$cardUuid])) {
            $this->collectionCards[$cardUuid]     = [$finish => $change];
            $this->collectionCardsList[$cardUuid] = [
                $finish => $attributes['updated'],
            ];

            return;
        }

        if (!isset($this->collectionCards[$cardUuid][$finish])) {
            $this->collectionCards[$cardUuid][$finish]     = $change;
            $this->collectionCardsList[$cardUuid][$finish] = $attributes['updated'];

            return;
        }

        $current                                                   = $this->collectionCards[$cardUuid][$finish];
        $this->collectionCards[$cardUuid][$finish]                 = $current + $change;
        $currentList                                               = $this->collectionCardsList[$cardUuid][$finish];
        $this->collectionCardsList[$cardUuid][$finish]['quantity'] = $currentList['quantity'] + $change;
        $this->collectionCardsList[$cardUuid][$finish]['price']    = $attributes['updated']['price'];
    }
}
