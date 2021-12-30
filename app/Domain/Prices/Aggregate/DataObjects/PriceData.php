<?php

namespace App\Domain\Prices\Aggregate\DataObjects;

class PriceData
{
    public ?int $id;

    public string $card_uuid;

    public string $provider_uuid;

    public float $price;

    public bool $foil;

    public string $type;
    
    public function __construct(array $data)
    {
        $this->id               = $data['id'] ?? null;
        $this->card_uuid        = $data['card_uuid'] ?? '';
        $this->provider_uuid    = $data['provider_uuid'] ?? '';
        $this->price            = $data['price'] ?? 0.0;
        $this->foil             = $data['foil'] ?? false;
        $this->type             = $data['type'] ?? 'usd';
    }

    public function toArray() : array
    {
        return [
            'id'            => $this->id,
            'card_uuid'     => $this->card_uuid,
            'provider_uuid' => $this->provider_uuid,
            'price'         => $this->price,
            'foil'          => $this->foil,
            'type'          => $this->type,
        ];
    }
}