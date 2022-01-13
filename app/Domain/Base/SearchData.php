<?php

namespace App\Domain\Base;

use App\App\Contracts\DataObjectInterface;

abstract class SearchData implements DataObjectInterface
{
    public array $filters;

    public ?array $paginator;

    public array $sort;

    public array $sort_order;

    public function __construct(array $data)
    {
        $this->filters      = $data['filters'] ?? [];
        $this->paginator    = $data['paginator'] ?? [];
        $this->sort         = $data['sort'] ?? [];
        $this->sort_order   = $data['sort_order'] ?? [];
    }

    public function toArray() : array
    {
        return [
            'filters'       => $this->filters,
            'paginator'     => $this->paginator,
            'sort'          => $this->sort,
            'sort_order'    => $this->sort_order,
        ];
    }
}
