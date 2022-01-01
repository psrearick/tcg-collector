<?php

namespace App\Domain\Cards\DataObjects;

class CardSearchData
{
    public ?string $card;

    public ?string $finish;

    public ?array $paginator;

    public ?string $set;

    public ?array $sort;

    public ?string $uuid;

    public function __construct(array $data)
    {
        $this->uuid         = $data['uuid'] ?? '';
        $this->card         = $data['card'] ?? '';
        $this->paginator    = $data['paginator'] ?? [];
        $this->set          = $data['set'] ?? '';
        $this->sort         = $data['sort'] ?? [];
        $this->finish       = $data['finish'] ?? '';
    }

    public function toArray() : array
    {
        return [
            'uuid'          => $this->uuid,
            'card'          => $this->card,
            'paginator'     => $this->paginator,
            'set'           => $this->set,
            'sort'          => $this->sort,
            'finish'        => $this->finish,
        ];
    }
}
