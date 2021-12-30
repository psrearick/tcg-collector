<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Aggregate\PriceAggregateRoot;
use Illuminate\Support\Str;
use App\Domain\Prices\Aggregate\DataObjects\PriceProviderData;

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
