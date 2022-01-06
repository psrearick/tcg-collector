<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;

class GetAllCollectionCards
{
    public function __invoke(?SupportCollection $collection = null) : SupportCollection
    {
        if ($collection !== null) {
            return $collection->mapToGroups(function ($summary) {
                return [$summary->card_uuid => $summary];
            })->map(function ($summaryGroup) {
                return $this->formatCollectionCards($summaryGroup);
            })->values();
        }

        $collections        = Collection::with(['user'])->get();
        $collectionUuids    = $collections->pluck('uuid')->toArray();

        return CollectionCardSummary::whereHas('collection', function (Builder $builder) use ($collectionUuids) {
            $builder->whereIn('collection_uuid', $collectionUuids);
        })
            ->with(['collection', 'card'])
            ->get();
    }

    private function formatCollectionCards(SupportCollection $collectionCards) : Card
    {
        return $collectionCards->reduce(function ($carry, $collectionCard) {
            if (!$carry) {
                $carry = $collectionCard->card;
            }

            $quantity   = $collectionCard->quantity;
            $finish     = $collectionCard->finish;
            $current    = $collectionCard->current_price;
            $collection = $collectionCard->collection_uuid;

            $quantities = $carry->quantities ?: [
                'nonfoil'   => 0,
                'foil'      => 0,
                'etched'    => 0,
                'total'     => 0,
            ];

            $quantities[$finish] += $quantity;
            $quantities['total'] += $quantity;

            $carry->quantities = $quantities;

            $collected = $carry->collected ?: [];
            if (!isset($collected[$collection])) {
                $collected[$collection]['collection'] = $collectionCard->collection;
                $collected[$collection]['quantities'] = [
                    'nonfoil'   => 0,
                    'foil'      => 0,
                    'etched'    => 0,
                    'total'     => 0,
                ];
            }
            $collected[$collection]['quantities'][$finish] += $quantity;
            $collected[$collection]['quantities']['total'] += $quantity;
            $carry->collected = $collected;

            return $carry;
        });
    }
}
