<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\Models\Card;
use Illuminate\Support\Str;

class GetCardFeatures
{
    private $card;

    public function __construct(Card $card)
    {
        $this->card = $card;
    }

//    public function getAlternateArt() : bool
//    {
//        return !!$this->card->isAlternate;
//    }

//    public function getAlternateArtString() : string
//    {
//        return $this->getAlternateArt() ? 'alternate art' : '';
//    }

    public function getBorderColor() : string
    {
        return $this->card->borderColor ?: '';
    }

    public function getBorderColorString() : string
    {
        $borderColor = $this->getBorderColor();

        if (!$borderColor) {
            return '';
        }

        if ($borderColor == 'black') {
            return '';
        }

        if ($borderColor == 'borderless') {
            return Str::ucfirst($borderColor);
        }

        return Str::ucfirst($borderColor) . ' Border';
    }

    // public function getFoilOnly() : bool
    // {
    //     return $this->card->hasFoil && !$this->card->hasNonFoil;
    // }

    // public function getFoilOnlyString() : string
    // {
    //     return $this->getFoilOnly() ? 'Foil-Only' : '';
    // }

    public function getFrameEffects() : array
    {
        return $this->getAllFrameEffects()['frameEffects'];
    }

    public function getFrameEffectsString() : string
    {
        return implode(', ', $this->getAllFrameEffects()['frameEffectsStrings']);
    }

    public function getFullArt() : bool
    {
        return !!$this->card->isFullArt;
    }

    public function getFullArtString() : string
    {
        return $this->getFullArt() ? 'Full Art' : '';
    }

    public function getLanguageCode() : string
    {
        $code = strtoupper($this->card->languageCode);
        if ($code == 'EN') {
            return '';
        }

        return Str::upper($code);
    }

    public function getLayout() : string
    {
        return $this->card->layout ?: '';
    }

    public function getLayoutString() : string
    {
        if (!$this->getLayout()) {
            return '';
        }

        if ($this->getLayout() == 'normal') {
            return '';
        }

        return Str::ucfirst($this->getLayout()) . ' Layout';
    }

    public function getPromo() : bool
    {
        return !!$this->card->isPromo;
    }

    public function getPromoString() : string
    {
        return $this->getPromo() ? 'Promo' : '';
    }

    public function getTextless() : bool
    {
        return !!$this->card->isTextless;
    }

    public function getTextlessString() : string
    {
        return $this->getTextless() ? 'Textless' : '';
    }

    public function getTimeshifted() : bool
    {
        return $this->card->frameVersion == 1997;
    }

    public function getTimeshiftedString() : string
    {
        return $this->getTimeshifted() ? 'Retro Frame' : '';
    }

    private function getAllFrameEffects() : array
    {
        $frameEffects        = [];
        $frameEffectsStrings = [];
        foreach ($this->card->frameEffects as $frameEffect) {
            $frameEffects[]        = Str::ucfirst($frameEffect->name);
            $frameEffectsStrings[] = Str::ucfirst($frameEffect->name) . ' Frame';
        }

        return [
            'frameEffects'          => $frameEffects,
            'frameEffectsStrings'   => $frameEffectsStrings,
        ];
    }
}
