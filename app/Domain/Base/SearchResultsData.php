<?php

namespace App\Domain\Base;

use App\App\Contracts\DataObjectInterface;
use App\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

abstract class SearchResultsData implements DataObjectInterface
{
    public ?Builder $builder;

    public ?Collection $collection;

    public ?SearchData $search;

    public function __construct(array $data)
    {
        $this->builder    = $data['builder'] ?? null;
        $this->collection = $data['collection'] ?? null;
        $this->search     = $data['search'] ?? null;
    }

    public function toArray() : array
    {
        return [
            'builder'       => $this->builder,
            'collection'    => $this->collection,
            'search'        => $this->search,
        ];
    }
}
