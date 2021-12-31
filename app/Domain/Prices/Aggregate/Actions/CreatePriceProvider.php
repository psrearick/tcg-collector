<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Aggregate\DataObjects\PriceProviderData;
use App\Domain\Prices\Aggregate\PriceAggregateRoot;
use App\Domain\Prices\Models\PriceProvider;
use Illuminate\Support\Str;

class createPriceProvider
{
    public function __invoke(array $priceProvider) : string
    {
        $data     = (new PriceProviderData($priceProvider))->toArray();

        $provider = PriceProvider::where('name', '=', $data['name'])->first();
        if ($provider) {
            return $provider->uuid;
        }

        $newUuid  = Str::uuid();
        PriceAggregateRoot::retrieve($newUuid)
            ->createPriceProvider($data)
            ->persist();

        return $newUuid;
    }
}
