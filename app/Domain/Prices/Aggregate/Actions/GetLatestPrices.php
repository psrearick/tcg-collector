<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Models\PriceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetLatestPrices
{
    public function __invoke(array $uuids) : Collection
    {
        $acceptableTypes = ['usd', 'usd_foil', 'usd_etched'];

        return DB::table('prices as p1')
            ->select(['p1.*', 'cards.name_normalized', 'cards.set_id'])
            ->leftJoin('prices as p2', function ($join) {
                $join->on('p1.card_uuid', '=', 'p2.card_uuid')
                    ->on('p1.type', '=', 'p2.type')
                    ->on('p1.created_at', '<', 'p2.created_at');
            })
            ->leftJoin('cards', 'cards.uuid', '=', 'p1.card_uuid')
        ->whereIn('p1.card_uuid', $uuids)
        ->whereIn('p1.type', $acceptableTypes)
        ->whereNull('p2.id')
        ->get();
    }
}
