<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Prices\Aggregate\DataObjects\PriceData;
use App\Domain\Prices\Aggregate\PriceAggregateRoot;
use Brick\Money\Money;
use Illuminate\Support\Str;

class CreatePrice
{
    public function __invoke(array $price) : string
    {
        $newUuid        = Str::uuid();
        $price['price'] = Money::of($price['price'] ?? 0, 'USD')
            ->getMinorAmount()->toInt();

        $data     = (new PriceData($price))->toArray();
        PriceAggregateRoot::retrieve($newUuid)
            ->createPrice($data)
            ->persist();

        return $newUuid;
    }
}
