<?php

namespace App\Domain\Cards\DataObjects;

use App\App\Contracts\CardSearchDataInterface;
use App\Domain\Base\SearchData;

class CardSearchData extends SearchData implements CardSearchDataInterface
{
    public ?string $card;

    public ?string $finish;

    public ?string $set;

    public ?int $set_id;

    public ?string $uuid;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->card         = $data['card'] ?? '';
        $this->finish       = $data['finish'] ?? '';
        $this->set          = $data['set'] ?? '';
        $this->set_id       = $data['set_id'] ?? null;
        $this->uuid         = $data['uuid'] ?? '';
    }

    public function toArray() : array
    {
        return array_merge(parent::toArray(), [
            'card'          => $this->card,
            'finish'        => $this->finish,
            'set'           => $this->set,
            'set_id'        => $this->set_id,
            'uuid'          => $this->uuid,
        ]);
    }
}
