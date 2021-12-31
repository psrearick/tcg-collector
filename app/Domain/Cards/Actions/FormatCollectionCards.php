<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardData;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Aggregate\Queries\CollectionCardsSummary;
use App\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FormatCollectionCards
{
    public function __invoke(Builder $builder, $perPage = 25, ?CollectionCardSearchData $collectionCardSearchData = null)
    {
        $collection = $collectionCardSearchData->uuid;
        $search     = $collectionCardSearchData->search;

        $collectionMap = [];
        if ($collection) {
            $collectionMap = (new CollectionCardsSummary($collection))->cards();
        }

        if ($search->paginator) {
            $page = $search->paginator;

            $collection = $builder->paginate(
                $page['per_page'] ?? 25, ['*'], 'page', $page['current_page'] ?? null
            );
        }

        if (!$collection) {
            $collection = $builder->paginate(25);
        }

        return tap($collection, function ($paginatedInstance) use ($collectionMap) {
            return $paginatedInstance->getCollection()->transform(function ($model) use ($collectionMap) {
                $cardBuilder = new BuildCard($model);
                $cardBuilt = $cardBuilder
                ->add('feature')
                ->add('allPrices')
                ->add('image_url')
                ->add('set_image_url')
                ->get();

                $card = $this->format($cardBuilt);

                return (new CardData([
                    'id'                => $card['id'],
                    'uuid'              => $card['uuid'],
                    'name'              => $card['name'],
                    'set_code'          => $card['set']['code'] ?? '',
                    'set_name'          => $card['set']['name'] ?? '',
                    'collected'         => [],
                    'prices'            => $card['prices'],
                    'quantities'        => $collectionMap[$card['uuid']] ?? [],
                    'features'          => $card['feature'],
                    'finishes'          => $card['finishes'],
                    'image'             => $card['image_url'],
                    'set_image'         => $card['set_image_url'],
                    'collector_number'  => $card['collectorNumber'] ?? '',
                ]))->toArray();
            });
        });
    }

    protected function format(Card $card) : array
    {
        $prices     = [];
        $finishes   = $card->finishes->pluck('name');
        $priceMap   = $card->prices->filter(function ($price) {
            return $price->price && in_array($price->type, [
                'usd',
                'usd_foil',
                'usd_etched',
            ]);
        })->map(function ($price) {
            return [
                'price' => $price->price,
                'type'  => $price->type,
            ];
        });

        foreach ($finishes as $finish) {
            $prices[$finish] = match ($finish) {
                'nonfoil'   => $priceMap->where('type', '=', 'usd')->first()['price'] ?? 0,
                'foil'      => $priceMap->where('type', '=', 'usd_foil')->first()['price'] ?? 0,
                'etched'    => $priceMap->where('type', '=', 'usd_etched')->first()['price'] ?? 0,
                default     => $priceMap->where('type', '=', 'usd')->first()['price'] ?? 0,
            };
        }

        $finishesMap = [];
        $finishes->each(function ($finish) use (&$finishesMap) {
            $finishesMap[$finish] = Str::ucfirst($finish);
        });

        $result             = $card->toArray();
        $result['finishes'] = $finishesMap;
        $result['prices']   = $prices;

        return $result;
    }
}
