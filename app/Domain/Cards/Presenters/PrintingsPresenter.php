<?php

namespace App\Domain\Cards\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Actions\GetPrintings;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\GetLatestPricesByFinish;
use Illuminate\Support\Str;

class PrintingsPresenter implements PresenterInterface
{
    private string $oracleId;

    public function __construct(string $oracleId)
    {
        $this->oracleId = $oracleId;
    }

    public function present() : array
    {
        return (new GetPrintings)($this->oracleId)
            ->load('set', 'finishes')
            ->filter(fn ($card) => (bool) $card->set)
            ->map(function (Card $card) {
                $cardBuild = (new BuildCard($card))
                    ->add('feature')
                    ->add('image_url')
                    ->add('set_image_url')
                    ->get();

                $printing                   = (object) $card->only(['rarity', 'id', 'uuid']);
                $printing->prices           = (new GetLatestPricesByFinish)($card->uuid);
                $printing->set_name         = $card->set->name;
                $printing->set_code         = $card->set->code;
                $printing->name             = $card->name;
                $printing->features         = $cardBuild['feature'];
                $printing->image            = $cardBuild['image_url'];
                $printing->set_image        = $cardBuild['set_image_url'];
                $printing->collector_number = $card->collectorNumber;
                $printing->rarity           = Str::headline($card->rarity);
                $printing->release_date     = $card->releaseDate;
                $printing->oracle_id        = $card->oracleId;

                return $printing;
            })
            ->sortByDesc('release_date')
            ->values()
            ->toArray();
    }
}
