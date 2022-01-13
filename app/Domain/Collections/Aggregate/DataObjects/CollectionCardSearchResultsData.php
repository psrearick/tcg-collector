<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;
use Illuminate\Database\Eloquent\Builder;

class CollectionCardSearchResultsData implements DataObjectInterface
{
    public ?Builder $builder;

    public function __construct(array $data)
    {
        $this->builder = $data['builder'] ?? null;
    }

    public function toarray() : array
    {
        return [
            'builder' => $this->builder,
        ];
    }
}
