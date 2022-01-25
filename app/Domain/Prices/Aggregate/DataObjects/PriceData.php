<?php

namespace App\Domain\Prices\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;

class PriceData implements DataObjectInterface
{
    public string $card_uuid;

    public bool $foil;

    public ?int $id;

    public int $price;

    public string $provider_uuid;

    public string $type;

    public function __construct(array $data)
    {
        $this->id               = $data['id'] ?? null;
        $this->card_uuid        = $data['card_uuid'] ?? '';
        $this->provider_uuid    = $data['provider_uuid'] ?? '';
        $this->price            = $data['price'] ?? 0;
        $this->type             = $data['type'] ?? 'usd';
    }

    public function toArray() : array
    {
        return [
            'id'            => $this->id,
            'card_uuid'     => $this->card_uuid,
            'provider_uuid' => $this->provider_uuid,
            'price'         => $this->price,
            'type'          => $this->type,
        ];
    }
}
