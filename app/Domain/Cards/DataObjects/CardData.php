<?php

namespace App\Domain\Cards\DataObjects;

class CardData
{
    public array $collected;

    public string $collector_number;

    public string $features;

    public array $finishes;

    public int $id;

    public string $image;

    public string $name;

    public string $name_normalized;

    public array $prices;

    public array $quantities;

    public string $set_code;

    public string $set_name;

    public string $setImage;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->id               = $data['id'] ?? null;
        $this->uuid             = $data['uuid'] ?? '';
        $this->name             = $data['name'] ?? '';
        $this->name_normalized  = $data['name_normalized'] ?? '';
        $this->set_code         = strtoupper($data['set_code'] ?? '');
        $this->set_name         = $data['set_name'] ?? '';
        $this->collected        = $data['collected'] ?? [];
        $this->features         = $data['features'] ?? '';
        $this->prices           = $data['prices'] ?? [];
        $this->quantities       = $data['quantities'] ?? [];
        $this->finishes         = $data['finishes'] ?? [];
        $this->image            = $data['image'] ?? '';
        $this->set_image        = $data['set_image'] ?? '';
        $this->collector_number = $data['collector_number'] ?? '';
    }

    public function toArray() : array
    {
        return [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'name'             => $this->name,
            'name_normalized'  => $this->name_normalized,
            'set_name'         => $this->set_name,
            'set_code'         => $this->set_code,
            'collected'        => $this->collected,
            'features'         => $this->features,
            'prices'           => $this->prices,
            'quantities'       => $this->quantities,
            'finishes'         => $this->finishes,
            'image'            => $this->image,
            'set_image'        => $this->set_image,
            'collector_number' => $this->collector_number,
        ];
    }
}
