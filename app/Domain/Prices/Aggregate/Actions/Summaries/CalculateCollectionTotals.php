<?php

namespace App\Domain\Prices\Aggregate\Actions\Summaries;

use App\Actions\GetGainLossValues;
use App\Domain\Base\Collection;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class CalculateCollectionTotals
{
    private GetGainLossValues $getGainLossValues;

    public function __construct(GetGainLossValues $getGainLossValues)
    {
        $this->getGainLossValues = $getGainLossValues;
    }

    public function execute(Collection $collection) : array
    {
        $totals = $collection->cards->mapToGroups(function (Card $card) {
            return [$card->uuid => $card];
        })
            ->filter(fn (EloquentCollection $cards) => $cards->sum('pivot.quantity') > 0)
            ->reduce(function (array $carry, EloquentCollection $cards) {
                $cardTotals = $cards->reduce(function (array $carry, Card $card) {
                    $count      = $card->pivot->quantity;

                    $carry['total_cards'] += $count;
                    $carry['current_value'] += $count * $this->price($card, $card->pivot->finish);
                    $carry['acquired_value'] += $count * $card->pivot->price_when_added;

                    return $carry;
                }, $this->base());

                $carry['total_cards'] += $cardTotals['total_cards'];
                $carry['current_value'] += $cardTotals['current_value'];
                $carry['acquired_value'] += $cardTotals['acquired_value'];

                return $carry;
            }, $this->base());

        $gainLoss = $this->getGainLossValues->execute($totals['current_value'], $totals['acquired_value']);

        $totals['gain_loss']         = $gainLoss['gain_loss'];
        $totals['gain_loss_percent'] = $gainLoss['gain_loss_percent'];

        return $totals;
    }

    private function base() : array
    {
        return [
            'total_cards'       => 0,
            'current_value'     => 0,
            'acquired_value'    => 0,
            'gain_loss'         => 0,
            'gain_loss_percent' => 0,
        ];
    }

    private function price(Card $card, string $finish) : int
    {
        return $card
            ->prices
            ->where('type', '=', app(MatchFinish::class)->execute($finish))
            ->sortByDesc('id')
            ->take(1)
            ->first()
            ->price;
    }
}
