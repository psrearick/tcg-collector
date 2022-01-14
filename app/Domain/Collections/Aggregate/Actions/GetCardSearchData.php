<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Models\CardSearchDataObject;

class GetCardSearchData
{
    private string $uuid;

    public function __invoke(string $uuid, bool $create = false) : CollectionCardSearchData
    {
        $this->uuid = $uuid;
        $data       = $this->getData();

        if ($create) {
            $this->create($data);
        }

        return new CollectionCardSearchData($data);
    }

    private function create(array $data) : void
    {
        CardSearchDataObject::create($data);
    }

    private function getData() : array
    {
        $card = Card::where('uuid', '=', $this->uuid)->with(['frameEffects', 'set', 'finishes', 'prices'])->first();

        if ($card->isOnlineOnly) {
            return [];
        }

        if (!$card->set) {
            return [];
        }

        if (!$card->set->id) {
            return [];
        }
        $prices = (new GetLatestPrices)([$card->uuid]);
        $prices->transform(function ($price) {
            $price->type = (new MatchType)($price->type);

            return $price;
        });
        $prices      = $prices->pluck('price', 'type')->toArray();

        $cardBuilder = new BuildCard($card);
        $build       = $cardBuilder
                    ->add('feature')
                    ->add('image_url')
                    ->add('set_image_url')
                    ->get();

        $finishes = $build->finishes->pluck('name')->toArray();

        return [
            'collector_number'  => $build->collectorNumber,
            'features'          => $build->feature,
            'finishes'          => $finishes,
            'id'                => $build->id,
            'image'             => $build->image_url,
            'name'              => $build->name,
            'name_normalized'   => $build->name_normalized,
            'prices'            => $prices,
            'set_id'            => optional($build->set)->id,
            'set_code'          => optional($build->set)->code,
            'set_image'         => $build->set_image_url,
            'set_name'          => optional($build->set)->name,
            'uuid'              => $build->uuid,
        ];
    }
}
