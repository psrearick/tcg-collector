<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domains\Users\DataObjects\UserData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;

class GetAllCollectionCards
{
    private SupportCollection $collections;

    public function __invoke(?SupportCollection $collection = null) : EloquentCollection
    {
        if ($collection !== null) {
            $this->collections = $this->getCollections();

            return $collection
                ->transform(function ($collectionCard) {
                    return $this->formatCollectionCards($collectionCard);
                });
        }

        $collections        = Collection::with(['user'])->get();
        $collectionUuids    = $collections->pluck('uuid')->toArray();

        return Card::whereHas('collections', function (Builder $builder) use ($collectionUuids) {
                $builder->whereIn('uuid', $collectionUuids);
            })
            ->with('collections')
            ->get();
    }

    private function formatCollectionCards(Card $collectionCard) : Card
    {
        $collections        = $this->collections;

        $totals = $collectionCard->collections->reduce(function ($carry, $collection) use ($collections) {
            $pivot = $collection->pivot;
            if (!$carry) {
                $carry = [];
            }

            $carry['quantities'] = $carry['quantities'] ?? [];
            $carry['quantities'][$pivot->finish] = $carry['quantities'][$pivot->finish] ?? 0;
            $carry['quantities'][$pivot->finish] += $pivot->quantity;

            $carry['collected'] = $carry['collected'] ?? [];
            if (!isset($carry['collected'][$collection->uuid])) {
                $carry['collected'][$collection->uuid]['collection'] = $collections->where('uuid', '=', $collection->uuid)->first();
                $carry['collected'][$collection->uuid]['quantities'] = [
                    'nonfoil'   => 0,
                    'foil'      => 0,
                    'etched'    => 0,
                ];
            }
            $carry['collected'][$collection->uuid]['quantities'][$pivot->finish] += $pivot->quantity;

            return $carry;
        });

        $collectionCard->quantities   = $totals['quantities'];
        $collectionCard->collected    = $totals['collected'];

        return $collectionCard;
    }

    private function getCollections() : SupportCollection
    {
        $collections        = Collection::with(['user'])->get();

        return $collections->map(function ($collection) {
            $collectionData = new CollectionData($collection->getAttributes());
            $collectionData->user = new UserData($collection->user->getAttributes());

            return $collectionData;
        });
    }
}
