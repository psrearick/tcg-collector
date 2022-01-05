<?php

namespace App\Domain\Prices\Aggregate\Actions;

class MatchType
{
    public function __invoke(string $type) : string
    {
        return match ($type) {
            'usd'           => 'nonfoil',
            'usd_foil'      => 'foil',
            'usd_etched'    => 'etched',
            default         => '',
        };
    }
}
