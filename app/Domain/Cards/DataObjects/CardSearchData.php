<?php

namespace App\Domain\Cards\DataObjects;

class CardSearchData
{
    public ?string $card;

    public ?array $paginator;

    public ?string $set;

    public ?array $sort;

    public function __construct(array $data)
    {
        $this->card         = $data['card'] ?? '';
        $this->paginator    = $data['paginator'] ?? [];
        $this->set          = $data['set'] ?? '';
        $this->sort         = $data['sort'] ?? [];
    }

    public function toArray() : array
    {
        return [
            'card'          => $this->card,
            'paginator'     => $this->paginator,
            'set'           => $this->set,
            'sort'          => $this->sort,
        ];
    }
}
