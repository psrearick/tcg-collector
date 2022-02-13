<?php

namespace App\Domain\Cards\DataObjects;

class CardSearchDataObjectData extends CoreCardData
{
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->name             = $data['card_name'] ?? '';
        $this->name_normalized  = $data['card_name_normalized'] ?? '';
        $this->uuid             = $data['card_uuid'] ?? '';
    }
}
