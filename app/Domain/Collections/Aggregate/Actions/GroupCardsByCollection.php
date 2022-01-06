<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Support\Collection as AppCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;

class GroupCardsByCollection
{
    public function __invoke(SupportCollection $collectionCards, bool $inGroup = false) : AppCollection
    {
        dd($collectionCards);

        // return new AppCollection($collection->all());
    }
}
