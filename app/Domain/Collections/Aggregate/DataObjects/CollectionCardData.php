<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

class CollectionCardData
{
    public string $acquired_date;

    public float $acquired_price;

    public string $collector_number;

    public string $features;

    public string $finish;

    public int $id;

    public string $image;

    public string $name;

    public string $name_normalized;

    public float $price;

    public int $quantity;

    public string $set;

    public string $condition;

    public string $set_image;

    public string $set_name;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->id               = $data['id'] ?? null;
        $this->uuid             = $data['uuid'] ?? null;
        $this->name             = $data['name'] ?? '';
        $this->name_normalized  = $data['name_normalized'] ?? '';
        $this->set              = strtoupper($data['set'] ?? '');
        $this->set_name         = $data['set_name'] ?? '';
        $this->features         = $data['features'] ?? '';
        $this->price            = $data['price'] ?? 0.0;
        $this->acquired_date    = $data['acquired_date'] ?? '';
        $this->acquired_price   = $data['acquired_price'] ?? 0.0;
        $this->quantity         = $data['quantity'] ?? 0;
        $this->finish           = $data['finish'] ?? 'nonfoil';
        $this->condition        = $data['condition'] ?? '';
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
            'set'              => $this->set,
            'set_name'         => $this->set_name,
            'features'         => $this->features,
            'price'            => $this->price,
            'acquired_date'    => $this->acquired_date,
            'acquired_price'   => $this->acquired_price,
            'quantity'         => $this->quantity,
            'finish'           => $this->finish,
            'condition'        => $this->condition,
            'image'            => $this->image,
            'set_image'        => $this->set_image,
            'collector_number' => $this->collector_number,
        ];
    }
}
