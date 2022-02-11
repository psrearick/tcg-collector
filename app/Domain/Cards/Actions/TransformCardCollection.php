<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardData;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class TransformCardCollection
{
    public function __invoke(Collection $results, array $collectionMap) : Collection
    {
        $prices = ((new GetLatestPrices)($results->pluck('uuid')->toArray()))->mapToGroups(function ($price) {
            $price->finish = (new MatchType)($price->type);

            return [$price->card_uuid => $price];
        })->map(function ($group) {
            $filtered = $group->filter(fn ($price) => $price->price > 0)->pluck('price', 'finish')->toArray();

            foreach ($filtered as $finish => $price) {
                $filtered["display_$finish"] = Money::ofMinor($price, 'USD')->formatTo('en_US');
            }

            return $filtered;
        });

        return $results->transform(function ($model) use ($collectionMap, $prices) {
            $card = (new BuildCard($model))
            ->add('feature')
            ->add('image_url')
            ->add('set_image_url')
            ->get();

            return (new CardData([
                'id'                => $card['id'],
                'uuid'              => $card['uuid'],
                'name'              => $card['name'],
                'set_code'          => $card['set']['code'] ?? '',
                'set_name'          => $card['set']['name'] ?? '',
                'prices'            => $prices[$card['uuid']] ?? [],
                'quantities'        => $collectionMap[$card['uuid']] ?? [],
                'features'          => $card['feature'],
                'finishes'          => $card->finishes->pluck('name')->values()->toArray(),
                'image'             => $card['image_url'],
                'set_image'         => $card['set_image_url'],
                'collector_number'  => $card['collectorNumber'] ?? '',
            ]))->toArray();
        });
    }
}
