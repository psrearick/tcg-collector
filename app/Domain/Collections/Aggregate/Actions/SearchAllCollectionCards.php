<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Actions\PaginateSearchResults;
use App\Domain\Cards\Actions\SearchCards;
use App\Domain\Cards\DataObjects\CardData;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Support\Collection as SupportCollection;
use Illuminate\Support\Collection;

class SearchAllCollectionCards
{
    public function __invoke(array $request)
    {
        $cardSearchData = new CardSearchData($request);
        if (!$cardSearchData->card && !$cardSearchData->set) {
            $data = new \App\Support\Collection([]);

            return (new PaginateSearchResults())($data,
                new CollectionCardSearchParameterData([
                    'search'    => $cardSearchData,
                    'data'      => $data,
                ])
            );
        }
        $data           = (new GetAllCollectionCards)();
        $builder        = $data;
        $builder
            ->leftJoin('cards', 'cards.uuid', '=', 'collection_card_summaries.card_uuid');
        $searchResults  = (new SearchCards)($cardSearchData, $builder)->builder ?: null;

        if ($searchResults) {
            $searchResults->with('cardSearchDataObject');
        }

        $cards      = $searchResults ? $searchResults->get() : collect([]);
        $collection = (new GetAllCollectionCards)($cards);
        $collection = $this->transformCollection($collection);
        $searchData = new CollectionCardSearchParameterData([
            'search'    => $cardSearchData,
            'data'      => $collection,
        ]);

        return (new PaginateSearchResults())($collection, $searchData);
    }

    private function transformCollection(Collection $collection) : Collection
    {
        $prices = ((new GetLatestPrices)($collection->pluck('uuid')->toArray()))->mapToGroups(function ($price) {
            $price->finish = (new MatchType)($price->type);

            return [$price->card_uuid => $price];
        })->map(function ($group) {
            return $group->filter(fn ($price) => $price->price > 0)->pluck('price', 'finish')->toArray();
        });

        $collection->transform(function (Card $card) use ($prices) {
            $cardData = $card->cardSearchDataObject;
            if (!$cardData) {
                $cardData = (new GetCardSearchData)($card->uuid, true);
            }

            return new CardData([
                'id'                => $cardData->id,
                'uuid'              => $cardData->card_uuid ?? $cardData->uuid,
                'name'              => $cardData->card_name ?? $cardData->name,
                'name_normalized'   => $cardData->card_name_normalized
                    ?? $cardData->name_normalized,
                'set'               => $cardData->set_code,
                'set_name'          => $cardData->set_name,
                'features'          => $cardData->features,
                'collected'         => $card->collected,
                'prices'            => $prices[$card->uuid],
                'quantities'        => $card->quantities,
                'finishes'          => $card->finishes->pluck('name')->values()->toArray(),
                'image'             => $cardData->image,
                'set_image'         => $cardData->set_image,
                'collector_number'  => $cardData->collector_number,
            ]);
        });

        return new SupportCollection($collection->all());
    }
}
