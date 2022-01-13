<?php

namespace App\Domain\Stores\DataObjects;

use App\App\Contracts\DataObjectInterface;
use App\Domain\Base\SearchParameterData;

class StoreSearchParameterData extends SearchParameterData implements DataObjectInterface
{
    public function __construct(array $data)
    {
        $this->data     = $data['data'] ?? null;
        $this->search   = $data['search'];
    }
}