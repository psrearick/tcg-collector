<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use App\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FormatCollectionCards
{
    public function __invoke(Collection $builder, CollectionCardSearchData $collectionCardSearchData) : LengthAwarePaginator
    {
        $search = $collectionCardSearchData->search;

        if ($search->paginator) {
            $page = $search->paginator;

            $paginated = $builder->paginate(
                $page['per_page'] ?? 25, null, $page['current_page'] ?? null, 'page'
            );
        }

        if (!isset($paginated)) {
            $paginated = $builder->paginate(25);
        }

        if (!$paginated) {
            return (new Collection([]))->paginate(25);
        }

        return $paginated;

        $prices = (new GetLatestPrices)(collect($paginated->items())->pluck('card_uuid')->toArray());

        return tap($paginated, function ($paginatedInstance) use ($prices) {
            return $paginatedInstance->getCollection()->transform(function ($model) use ($prices) {
                $card = $model->card;
                $finish = $model->finish;
                $type = (new MatchFinish)($finish);
                $price = $prices
                        ->where('type', '=', $type)
                        ->where('card_uuid', '=', $card['uuid'])
                        ->first();

                $cardBuilder = new BuildCard($card);
                $build = $cardBuilder
                    ->add('feature')
                    ->add('image_url')
                    ->add('set_image_url')
                    ->get();

                return (new CollectionCardData([
                    'id'                => $build->id,
                    'uuid'              => $build->uuid,
                    'name'              => $build->name,
                    'set'               => optional($build->set)->code,
                    'set_name'          => optional($build->set)->name,
                    'features'          => $build->feature,
                    'price'             => optional($price)->price,
                    'acquired_date'     => $model->date_added ?? null,
                    'acquired_price'    => $model->price_when_added ?? null,
                    'quantity'          => $model->quantity ?? null,
                    'finish'            => $model->finish ?? null,
                    'image'             => $build->image_url,
                    'set_image'         => $build->set_image_url,
                    'collector_number'  => $build->collectorNumber,
                ]))->toArray();
            });
        });
    }
}
