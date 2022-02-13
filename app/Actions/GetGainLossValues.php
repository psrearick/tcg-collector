<?php

namespace App\Actions;

class GetGainLossValues
{
    public function handle(int $currentValue, int $acquiredValue) : array
    {
        $gainLoss        = $currentValue - $acquiredValue;
        $gainLossPercent = $gainLoss === 0 ? 0 : 1;
        $gainLossPercent = $acquiredValue !== 0 ? $gainLoss / $acquiredValue : $gainLossPercent;

        return [
            'gain_loss'             => $gainLoss,
            'gain_loss_percent'     => $gainLossPercent,
        ];
    }
}
