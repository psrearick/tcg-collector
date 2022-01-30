<?php

namespace App\Domain\Cards\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use Brick\Money\Money;
use Illuminate\Support\Str;

class CardShowPresenter implements PresenterInterface
{
    private Card $card;

    public function __construct(string $uuid)
    {
        $this->card = optional(Card::uuid($uuid))
            ->load('set', 'finishes');
    }

    public function getPrices() : array
    {
        return (new GetLatestPrices)([$this->card->uuid])
            ->filter(fn ($price) => $price->price > 0)
            ->mapToGroups(function ($filtered){
                $finish = (new MatchType)($filtered->type);
                return [Str::headline($finish) => Money::ofMinor($filtered->price, 'USD')->formatTo('en_US')];
            })
            ->map(fn ($group) => $group->first())
            ->toArray();
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
            ->keyBy('format')
            ->map(fn ($legality) => $legality['status'])
            ->toArray();

    }

    public function present() : array
    {
        $prices = $this->getPrices();

        $cardBuilder = new BuildCard($this->card);
        $card = $cardBuilder
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
        ];
    }
}
