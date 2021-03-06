<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\Models\Card;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuildCard
{
    private Card $card;

    public function __construct(Card $card)
    {
        $this->card = $card;
    }

    public function add(string $attribute)
    {
        $method                   = 'get' . Str::studly($attribute);
        $this->card->{$attribute} = $this->{$method}();

        return $this;
    }

    /**
     * @return Card
     */
    public function get()
    {
        return $this->card;
    }

    /**
     * Get strifigied card features
     *
     * @return string
     */
    public function getFeature() : string
    {
        $features    = $this->getFeatures();
        $allFeatures = [
            $features['frameEffectsString'],
            $features['borderColorString'],
            $features['fullArtString'],
            //            $features['alternateArtString'],
            // $features['foilOnlyString'],
            $features['promoString'],
            $features['textlessString'],
            $features['timeshiftedString'],
            $features['layoutString'],
            $features['language'],
        ];
        $featureStrings = [];
        foreach ($allFeatures as $feature) {
            if ($feature && strlen($feature) > 0) {
                $featureStrings[] = $feature;
            }
        }

        return implode(', ', $featureStrings);
    }

    /**
     * Get an array of card features
     *
     * @return array
     */
    public function getFeatures() : array
    {
        $featureCollector = new GetCardFeatures($this->card);

        return [
            'frameEffects'        => $featureCollector->getFrameEffects(),
            'frameEffectsString'  => $featureCollector->getFrameEffectsString(),
            'borderColor'         => $featureCollector->getBorderColor(),
            'borderColorString'   => $featureCollector->getBorderColorString(),
            'fullArt'             => $featureCollector->getFullArt(),
            'fullArtString'       => $featureCollector->getFullArtString(),
            //            'alternateArt'        => $featureCollector->getAlternateArt(),
            //            'alternateArtString'  => $featureCollector->getAlternateArtString(),
            // 'foilOnly'            => $featureCollector->getFoilOnly(),
            // 'foilOnlyString'      => $featureCollector->getFoilOnlyString(),
            'promo'               => $featureCollector->getPromo(),
            'promoString'         => $featureCollector->getPromoString(),
            'textless'            => $featureCollector->getTextless(),
            'textlessString'      => $featureCollector->getTextlessString(),
            'timeshifted'         => $featureCollector->getTimeshifted(),
            'timeshiftedString'   => $featureCollector->getTimeshiftedString(),
            'layout'              => $featureCollector->getLayout(),
            'layoutString'        => $featureCollector->getLayoutString(),
            'language'            => $featureCollector->getLanguageCode(),
        ];
    }

    /**
     * Get image url, if there isn't one, get it from scryfall and save it
     *
     * @param bool $dispatch
     * @return string
     */
    public function getImageUrl(bool $dispatch = true) : string
    {
        return asset($this->card->imagePath);
    }

    public function getSetImageUrl() : string
    {
        if (!$this->card->set) {
            return '';
        }

        if (!$this->card->set->svgPath) {
            return '';
        }

        return Storage::url($this->card->set->svgPath);
    }

    private function getAllPrices() : array
    {
        $prices = $this->card->prices
            ->where('priceProvider.name', '=', 'scryfall')
            ->whereNotNull('type')
            ->where('type', '!=', '');

        if ($prices->isEmpty()) {
            return $this->getPricesWithoutType();
        }

        $priceArray = [];
        $prices->each(function ($price) use (&$priceArray) {
            if (!in_array($price->type, ['usd', 'usd_foil', 'usd_etched'])) {
                return;
            }
            $key = match ($price->type) {
                'usd'        => 'nonfoil',
                'usd_foil'   => 'foil',
                'usd_etched' => 'etched',
            };
            $priceArray[$key] = $price->price;
        });

        return $priceArray;
    }

    /**
     * Get card price, default to non foil
     *
     * @param bool $foil
     * @return mixed|null
     */
    private function getPrice(bool $foil = false) : ?int
    {
        return optional(
            $this->card->prices
                ->where('priceProvider.name', '=', 'tcgplayer')
                ->where('foil', $foil)
                ->first()
        )->price ?: optional(
            $this->card->prices
                ->where('priceProvider.name', '=', 'scryfall')
                ->where('foil', $foil)
                ->first()
        )->price;
    }

    private function getPricesWithoutType() : array
    {
        return [
            'nonfoil' => $this->getPrice(),
            'foil'    => $this->getPrice(true),
        ];
    }
}
