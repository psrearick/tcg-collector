<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Aggregate\DataObjects\PriceData;
use App\Domain\Prices\Aggregate\PriceAggregateRoot;
use Illuminate\Support\Str;

class createPrice
{
    public function __invoke(array $price) : string
    {
        $newUuid  = Str::uuid();
        $data     = (new PriceData($price))->toArray();
        PriceAggregateRoot::retrieve($newUuid)
            ->createPrice($data)
            ->persist();

        return $newUuid;
    }
}
