<?php

namespace App\Domain\Cards\DataObjects;

use App\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CardSearchResultsData
{
    public ?Builder $builder;

    public ?Collection $collection;

    public function __construct(array $data)
    {
        $this->builder    = $data['builder'] ?? null;
        $this->collection = $data['collection'] ?? null;
    }

    public function toarray() : array
    {
        return [
            'builder'    => $this->builder,
            'collection' => $this->collection,
        ];
    }
}
