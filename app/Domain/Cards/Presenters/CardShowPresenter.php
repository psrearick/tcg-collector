<?php

namespace App\Domain\Cards\Presenters;

use App\App\Contracts\PresenterInterface;
use App\App\Contracts\PresentsLegalities;
use App\App\Contracts\PresentsPrices;
use App\App\Contracts\PresentsPrintings;
use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\GetLatestPricesByFinish;
use Illuminate\Support\Str;

class CardShowPresenter implements PresenterInterface, PresentsPrices, PresentsLegalities, PresentsPrintings
{
    private Card $card;

    public function __construct(string $uuid)
    {
        $this->card = optional(Card::uuid($uuid))
            ->load('set', 'finishes');
    }

    public function getLegalities() : array
    {
        return $this->card->legalities
            ->map(function ($legality) {
                return [
                    'format'    => Str::headline($legality->format),
                    'status'    => Str::headline($legality->status),
                ];
            })
            ->sortBy('format')
            ->keyBy('format')
            ->map(fn ($legality) => $legality['status'])
            ->toArray();
    }

    public function getPrices() : array
    {
        return (new GetLatestPricesByFinish)($this->card->uuid);
    }

    public function getPrintings() : array
    {
        return (new PrintingsPresenter($this->card->oracleId))->present();
    }

    public function present() : array
    {
        $prices = $this->getPrices();

        $card = (new BuildCard($this->card))
            ->add('feature')
            ->add('image_url')
            ->add('set_image_url')
            ->get();

        return [
            'name'              => $card->name,
            'set_name'          => $card->set->name,
            'uuid'              => $card['uuid'],
            'set_code'          => $card->set->code ?? '',
            'prices'            => $prices ?? [],
            'features'          => $card['feature'],
            'image'             => $card['image_url'],
            'set_image'         => $card['set_image_url'],
            'collector_number'  => $card['collectorNumber'] ?? '',
            'legalities'        => $this->getLegalities(),
            'rarity'            => Str::headline($card->rarity),
            'type'              => $card->typeLine,
            'mana_cost'         => $card->manaCost,
            'oracle_text'       => $card->oracleText,
            'language'          => Str::upper($card->languageCode),
            'artist'            => $card->artist,
            'printings'         => $this->getPrintings(),
        ];
    }
}
