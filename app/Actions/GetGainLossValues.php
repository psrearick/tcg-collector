<?php

namespace App\Actions;

class GetGainLossValues
{
    public function execute(int $currentValue, int $acquiredValue) : array
    {
        $gainLoss        = $currentValue - $acquiredValue;
        $gainLossPercent = $gainLoss === 0 ? 0 : 1;
        $gainLossPercent = round($acquiredValue !== 0 ? $gainLoss / $acquiredValue : $gainLossPercent, 4);

        return [
            'gain_loss'             => $gainLoss,
            'gain_loss_percent'     => $gainLossPercent,
        ];
    }
}
