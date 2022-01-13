<?php

namespace App\Domain\Base;

use App\App\Contracts\DataObjectInterface;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Collection;
use App\Domain\Base\SearchData;

abstract class SearchParameterData implements DataObjectInterface
{
    public ?Builder $builder;

    public SearchData $search;

    public ?Collection $data;

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