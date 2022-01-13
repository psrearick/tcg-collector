<?php

namespace App\Domain\Cards\DataObjects;

use App\App\Contracts\DataObjectInterface;

class CardData extends CoreCardData implements DataObjectInterface
{
    public array $collected;

    public array $finishes;

    public array $prices;

    public array $quantities;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->collected        = $data['collected'] ?? [];
        $this->finishes         = $data['finishes'] ?? [];
        $this->prices           = $data['prices'] ?? [];
        $this->quantities       = $data['quantities'] ?? [];
    }

    public function toArray() : array
    {
        return array_merge(
            parent::toArray(),
            [
                'collected'        => $this->collected,
                'finishes'         => $this->finishes,
                'prices'           => $this->prices,
                'quantities'       => $this->quantities,
            ]);
    }
}
