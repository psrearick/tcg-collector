<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CollectionCardSearchData
{
    public ?Builder $builder;

    public ?Collection $data;

    public CardSearchData $search;

    public bool $single;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->builder  = $data['builder'] ?? null;
        $this->uuid     = $data['uuid'] ?? '';
        $this->data     = $data['data'] ?? null;
        $this->search   = $data['search'];
        $this->single   = $data['single'] ?? false;
    }

    public function toArray() : array
    {
        return [
            'builder'   => $this->builder,
            'uuid'      => $this->uuid,
            'search'    => $this->search,
            'data'      => $this->data,
            'single'    => $this->single,
        ];
    }
}
