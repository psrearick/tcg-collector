<?php

namespace App\Domain\Prices\Aggregate\Actions;

use Brick\Money\Money;
use Illuminate\Support\Str;

class GetLatestPricesByFinish
{
    public function __invoke(string $uuid) : array
    {
        return (new GetLatestPrices)([$uuid])
            ->filter(fn ($price) => $price->price > 0)
            ->mapToGroups(function ($filtered) {
                $finish = (new MatchType)($filtered->type);

                return [Str::headline($finish) => Money::ofMinor($filtered->price, 'USD')->formatTo('en_US')];
            })
            ->map(fn ($group) => $group->first())
            ->toArray();
    }
}
