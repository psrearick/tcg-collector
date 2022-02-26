<?php

namespace App\Domain\Prices\Aggregate\Actions;

class MatchFinish
{
    public function execute(string $finish) : string
    {
        return match ($finish) {
            'nonfoil'   => 'usd',
            'foil'      => 'usd_foil',
            'etched'    => 'usd_etched',
            default     => '',
        };
    }
}
