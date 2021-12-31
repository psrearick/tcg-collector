<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\Domain\Cards\DataObjects\CardSearchData;
use App\Support\Collection;

class CollectionCardSearchData
{
    public ?Collection $data;

    public CardSearchData $search;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->uuid     = $data['uuid'] ?? '';
        $this->data     = $data['data'] ?? null;
        $this->search   = $data['search'];
    }

    public function toArray() : array
    {
        return [
            'uuid'      => $this->uuid,
            'search'    => $this->search,
            'data'      => $this->data,
        ];
    }
}
