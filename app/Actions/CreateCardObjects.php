<?php

namespace App\Actions;

use App\Domain\Cards\Models\Card;
use App\Jobs\CreateCardSearchDataObjects;

class CreateCardObjects
{
    public function __invoke()
    {
        Card::with(['frameEffects', 'set', 'finishes', 'prices'])->chunk(30,
            function ($cards) {
                $cards->each(function ($card) {
                    CreateCardSearchDataObjects::dispatch($card);
                });
            }
        );
    }
}
