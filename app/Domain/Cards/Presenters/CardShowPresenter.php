<?php

namespace App\Domain\Cards\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Cards\Models\Card;

class CardShowPresenter implements PresenterInterface
{
    private Card $card;

    public function __construct(string $uuid)
    {
        $this->card = Card::uuid($uuid);
    }

    public function present() : array
    {
        return [];
    }
}