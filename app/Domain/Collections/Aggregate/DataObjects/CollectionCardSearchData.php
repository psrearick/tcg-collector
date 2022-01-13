<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;
use App\Domain\Base\SearchParameterData;

class CollectionCardSearchData extends SearchParameterData implements DataObjectInterface
{
    public function __construct(array $data)
    {
        $this->builder  = $data['builder'] ?? null;
        $this->uuid     = $data['uuid'] ?? '';
        $this->data     = $data['data'] ?? null;
        $this->search   = $data['search'];
        $this->single   = $data['single'] ?? false;
    }
}
