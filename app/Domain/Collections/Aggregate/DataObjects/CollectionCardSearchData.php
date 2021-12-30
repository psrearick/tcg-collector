<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\Domain\Cards\DataObjects\CardSearchData;

class CollectionCardSearchData
{
    public CardSearchData $search;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->uuid     = $data['uuid'];
        $this->search   = $data['search'];
    }

    public function toArray() : array
    {
        return [
            'uuid'      => $this->uuid,
            'search'    => $this->search,
        ];
    }
}
