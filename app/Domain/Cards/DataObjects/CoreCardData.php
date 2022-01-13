<?php

namespace App\Domain\Cards\DataObjects;

use App\App\Contracts\DataObjectInterface;

abstract class CoreCardData implements DataObjectInterface
{
    public string $collector_number;

    public string $features;

    public int $id;

    public string $image;

    public string $name;

    public string $name_normalized;

    public string $set_code;

    public string $set_image;

    public string $set_name;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->collector_number = $data['collector_number'] ?? '';
        $this->features         = $data['features'] ?? '';
        $this->id               = $data['id'] ?? null;
        $this->image            = $data['image'] ?? '';
        $this->name             = $data['name'] ?? '';
        $this->name_normalized  = $data['name_normalized'] ?? '';
        $this->set_code         = strtoupper($data['set_code'] ?? '');
        $this->set_image        = $data['set_image'] ?? '';
        $this->set_name         = $data['set_name'] ?? '';
        $this->uuid             = $data['uuid'] ?? '';
    }

    public function toArray() : array
    {
        return [
            'collector_number' => $this->collector_number,
            'features'         => $this->features,
            'id'               => $this->id,
            'image'            => $this->image,
            'name'             => $this->name,
            'name_normalized'  => $this->name_normalized,
            'set_code'         => $this->set_code,
            'set_image'        => $this->set_image,
            'set_name'         => $this->set_name,
            'uuid'             => $this->uuid,
        ];
    }
}
