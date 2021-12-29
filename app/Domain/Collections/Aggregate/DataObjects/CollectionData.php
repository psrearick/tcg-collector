<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

class CollectionData
{
    public string $description;

    public ?int $folder_id;

    public ?int $id;

    public string $name;

    public ?string $uuid;

    protected bool $is_public;

    public function __construct(array $data)
    {
        $this->uuid         = $data['uuid'] ?? null;
        $this->id           = $data['id'] ?? null;
        $this->folder_id    = $data['folder_id'] ?? null;
        $this->name         = $data['name'] ?? '';
        $this->description  = $data['description'] ?? '';
        $this->is_public    = $data['is_public'] ?? false;
    }

    public function toArray() : array
    {
        return [
            'uuid'          => $this->uuid,
            'id'            => $this->id,
            'folder_id'     => $this->folder_id,
            'name'          => $this->name,
            'description'   => $this->description,
            'is_public'     => $this->is_public,
        ];
    }
}
