<?php

namespace App\Domain\Collections\Aggregate\Projectors\Actions;

use App\Domain\Collections\Models\Collection;
use Carbon\Carbon;

class CreateCollectionCard
{
    public function __invoke(array $attributes) : void
    {
        Collection::uuid($attributes['uuid'])->cards()
            ->attach($attributes['updated']['id'], [
                'collection_uuid'  => $attributes['uuid'],
                'card_uuid'        => $attributes['updated']['uuid'],
                'price_when_added' => $attributes['updated']['acquired_price'],
                'quantity'         => $attributes['quantity_diff'],
                'finish'           => $attributes['updated']['finish'],
                'condition'        => $attributes['updated']['condition'] ?: 'NM',
                'date_added'       => Carbon::now(),
            ]);
    }
}
