<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Support\Collection as AppCollection;
use Illuminate\Support\Collection as SupportCollection;

class GroupCardsByCollection
{
    public function __invoke(SupportCollection $collectionCards, bool $inGroup = false) : AppCollection
    {

        // return new AppCollection($collection->all());
    }
}
