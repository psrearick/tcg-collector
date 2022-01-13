<?php

namespace App\Domain\Cards\DataObjects;

use App\App\Contracts\DataObjectInterface;
use App\Domain\Base\SearchData;

class CardSearchData extends SearchData implements DataObjectInterface
{
    public ?string $card;

    public array $filters;

    public ?string $finish;

    public ?array $paginator;

    public ?string $set;

    public ?int $set_id;

    public array $sort;

    public array $sort_order;

    public ?string $uuid;

    public function __construct(array $data)
    {
        $this->uuid         = $data['uuid'] ?? '';
        $this->card         = $data['card'] ?? '';
        $this->paginator    = $data['paginator'] ?? [];
        $this->set          = $data['set'] ?? '';
        $this->set_id       = $data['set_id'] ?? null;
        $this->sort         = $data['sort'] ?? [];
        $this->sort_order   = $data['sort_order'] ?? [];
        $this->finish       = $data['finish'] ?? '';
        $this->filters      = $data['filters'] ?? [];
    }

    public function toArray() : array
    {
        return [
            'uuid'          => $this->uuid,
            'card'          => $this->card,
            'paginator'     => $this->paginator,
            'set'           => $this->set,
            'set_id'        => $this->set_id,
            'sort'          => $this->sort,
            'finish'        => $this->finish,
            'sort_order'    => $this->sort_order,
            'filters'       => $this->filters,
        ];
    }
}
