<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FormatCollectionCards
{
    public function __invoke(Builder $builder, CollectionCardSearchData $collectionCardSearchData) : LengthAwarePaginator
    {
        $search = $collectionCardSearchData->search;

        if ($search->paginator) {
            $page = $search->paginator;

            $paginated = $builder->paginate(
                $page['per_page'] ?? 25, ['*'], 'page', $page['current_page'] ?? null
            );
        }

        if (!isset($paginated)) {
            $paginated = $builder->paginate(25);
        }

        if (!$paginated) {
            return (new Collection([]))->paginate(25);
        }

        $prices = $this->getPriceMap($paginated->pluck('card_uuid')->toArray());

        return tap($paginated, function ($paginatedInstance) use ($prices) {
            return $paginatedInstance->getCollection()->transform(function ($model) use ($prices) {
                $card = $model->card;
                $finish = $model->finish;
                $type = $this->matchFinish($finish);
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

    protected function matchFinish(string $finish) : string
    {
        return match ($finish) {
            'nonfoil'       => 'usd',
                'foil'      => 'usd_foil',
                'etched'    => 'usd_etched',
                default     => '',
        };
    }

    private function getPriceMap($uuids) : Collection
    {
        return DB::table('prices as p1')
            ->select(['p1.*', 'cards.name_normalized', 'cards.set_id'])
            ->leftJoin('prices as p2', function ($join) {
                $join->on('p1.card_uuid', '=', 'p2.card_uuid')
                    ->on('p1.type', '=', 'p2.type')
                    ->on('p1.created_at', '<', 'p2.created_at');
            })
            ->leftJoin('cards', 'cards.uuid', '=', 'p1.card_uuid')
        ->whereIn('p1.card_uuid', $uuids)
        ->whereNull('p2.id')
        ->get();
    }
}
