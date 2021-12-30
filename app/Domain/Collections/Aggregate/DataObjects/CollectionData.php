<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

class CollectionData
{
    public string $description;

    public ?string $folder_uuid;

    public ?int $id;

    public bool $is_public;

    public string $name;

    public ?int $user_id;

    public ?string $uuid;

    public function __construct(array $data)
    {
        $this->uuid         = $data['uuid'] ?? null;
        $this->id           = $data['id'] ?? null;
        $this->folder_uuid  = $data['folder_uuid'] ?? null;
        $this->name         = $data['name'] ?? '';
        $this->description  = $data['description'] ?? '';
        $this->is_public    = $data['is_public'] ?? false;
        $this->user_id      = $data['user_id'] ?? null;
    }

    public function toArray() : array
    {
        return [
            'uuid'          => $this->uuid,
            'id'            => $this->id,
            'folder_uuid'   => $this->folder_uuid,
            'name'          => $this->name,
            'description'   => $this->description,
            'user_id'       => $this->user_id,
            'is_public'     => $this->is_public,
        ];
    }
}
