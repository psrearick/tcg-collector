<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardData;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;

class FormatCards
{
    public function __invoke(Builder $builder, ?CollectionCardSearchParameterData $collectionCardSearchParameterData = null, $shouldPaginate = true)
    {
        $collection = $collectionCardSearchParameterData->uuid;
        $search     = $collectionCardSearchParameterData->search;

        $collectionMap = [];
        if ($collection) {
            CollectionCardSummary::where('collection_uuid', '=', $collection)->each(function ($collectionCard) use (&$collectionMap) {
                if (!isset($collectionMap[$collectionCard->card_uuid])) {
                    $collectionMap[$collectionCard->card_uuid] = [];
                }
                $collectionMap[$collectionCard->card_uuid][$collectionCard['finish']] = $collectionCard['quantity'];
            });
        }

        if ($shouldPaginate) {
            return $this->getResultsPaginated($search, $builder, $collectionMap);
        }

        return $this->getResults($builder, $collectionMap);
    }

    private function getResults(Builder $builder, array $collectionMap)
    {
        return $this->transformResults($builder->get(), $collectionMap);
    }

    private function getResultsPaginated(CardSearchData $search, Builder $builder, array $collectionMap)
    {
        if ($search->paginator) {
            $page = $search->paginator;

            $paginated = $builder->paginate(
                $page['per_page'] ?? 25, ['*'], 'page', $page['current_page'] ?? null
            );
        } else {
            $paginated = $builder->paginate(25);
        }

        return tap($paginated, function ($paginatedInstance) use ($collectionMap) {
            return $this->transformResults($paginatedInstance->getCollection(), $collectionMap);
        });
    }

    private function transformResults(SupportCollection $results, array $collectionMap)
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
            $cardBuilder = new BuildCard($model);
            $card = $cardBuilder
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
                'prices'            => $prices[$card['uuid']],
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
