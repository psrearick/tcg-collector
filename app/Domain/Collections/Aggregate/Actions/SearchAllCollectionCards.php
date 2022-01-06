<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Actions\SearchCards;
use App\Domain\Cards\DataObjects\CardData;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\FormatCollectionCards;
use App\Domain\Collections\Aggregate\Actions\GetAllCollectionCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Support\Collection as SupportCollection;
use Illuminate\Support\Collection;

class SearchAllCollectionCards
{
    public function __invoke(array $request)
    {
        $data           = (new GetAllCollectionCards)();
        $cardSearchData = new CardSearchData($request);
        $builder        = $data->toQuery();
        $searchResults  = (new SearchCards)($cardSearchData, $builder)->builder ?: null;

        if ($searchResults) {
            $searchResults->with(['collections', 'set', 'finishes', 'frameEffects']);
        }

        $cards      = $searchResults ? $searchResults->get() : collect([]);
        $collection = (new GetAllCollectionCards)($cards);
        $collection = $this->transformCollection($collection);
        $searchData = new CollectionCardSearchData([
            'search'    => new CardSearchData($request),
            'data'      => $collection,
        ]);

        return (new FormatCollectionCards)($collection, $searchData);
    }

    private function transformCollection(Collection $collection) : Collection
    {
        $prices = ((new GetLatestPrices)($collection->pluck('uuid')->toArray()))->mapToGroups(function ($price) {
            $price->finish = (new MatchType)($price->type);

            return [$price->card_uuid => $price];
        })->map(function ($group) {
            return $group->filter(fn ($price) => $price->price > 0)->pluck('price', 'finish')->toArray();
        });

        $collection->transform(function (Card $model) use ($prices) {
            $cardBuilder = new BuildCard($model);
            $card = $cardBuilder
            ->add('feature')
            ->add('image_url')
            ->add('set_image_url')
            ->get();

            return new CardData([
                'id'               => $card->id,
                'uuid'             => $card->uuid,
                'name'             => $card->name,
                'set_name'         => $card->set->name,
                'set_code'         => $card->set->code,
                'collected'        => $card->collected,
                'features'         => $card->feature,
                'prices'           => $prices[$card->uuid],
                'quantities'       => $card->quantities,
                'finishes'         => $card->finishes->pluck('name')->values()->toArray(),
                'image'            => $card->image_url,
                'set_image'        => $card->set_image_url,
                'collector_number' => $card->collectorNumber,
            ]);
        });

        return new SupportCollection($collection->all());
    }
}
