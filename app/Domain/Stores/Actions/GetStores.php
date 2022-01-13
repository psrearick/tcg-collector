<?php

namespace App\Domain\Stores\Actions;

use App\Domain\Stores\DataObjects\StoreData;
use App\Domain\Stores\Models\Store;
use App\Support\Collection;
use DateTimeZone;

class GetStores
{
    public function __invoke() : Collection
    {
        $stores = new Collection(Store::all());
        $data   = $stores->map(function (Store $store) {
            $date = date_create($store->created_at, new DateTimeZone('UTC'));
            $date->setTimeZone(new DateTimeZone('America/New_York'));
            $data = new StoreData($store->toArray());
            $data->created_at = date_format($date, 'm-d-Y h:i a');

            return $data;
        });

        return $data;
    }
}
