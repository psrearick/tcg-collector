<?php

namespace App\Domain\Cards\DataObjects;

use Illuminate\Database\Eloquent\Builder;

class CardSearchResults
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
