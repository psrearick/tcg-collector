<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Aggregate\DataObjects\PriceProviderData;
use App\Domain\Prices\Aggregate\PriceAggregateRoot;
use Illuminate\Support\Str;

class createPriceProvider
{
    public function __invoke(array $priceProvider) : string
    {
        $newUuid  = Str::uuid();
        $data     = (new PriceProviderData($priceProvider))->toArray();
        PriceAggregateRoot::retrieve($newUuid)
            ->createPriceProvider($data)
            ->persist();

        return $newUuid;
    }
}
