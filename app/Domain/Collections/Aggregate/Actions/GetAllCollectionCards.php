<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;
use Illuminate\Support\Collection as SupportCollection;

class GetAllCollectionCards
{
    public function __invoke(?SupportCollection $collection = null)
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

        return CollectionCardSummary::whereIn('collection_uuid', $collectionUuids)
            ->where('quantity', '>', 0)
            ->whereNull('deleted_at');
    }

    private function formatCollectionCards(SupportCollection $collectionCards) : Card
    {
        return $collectionCards->reduce(function ($carry, $collectionCard) {
            if (!$carry) {
                $carry = $collectionCard->card;
            }

            $carry->cardSearchDataObject = $collectionCard->cardSearchDataObject;
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
