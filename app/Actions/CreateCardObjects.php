<?php

namespace App\Actions;

use App\Jobs\ChunkCardObjectCreate;
use App\Domain\Cards\Models\Card;
use App\Jobs\CreateCardSearchDataObjects;

class CreateCardObjects
{
    public function __invoke()
    {
        Card::chunkById(150,
            function ($cards) {
                $cards->each(function ($card) {
                    CreateCardSearchDataObjects::dispatch($card->uuid);
                });
            }
        );
        // ChunkCardObjectCreate::dispatch();
    }
}
