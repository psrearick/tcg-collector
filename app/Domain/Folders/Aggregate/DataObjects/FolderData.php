<?php

namespace App\Domain\Folders\Aggregate\DataObjects;

class FolderData
{
    public string $description;

    public ?int $id;

    public bool $is_public;

    public string $parent_uuid;

    public string $name;

    public ?int $user_id;

    public ?string $uuid;    

    public function __construct(array $data)
    {
        $this->uuid         = $data['uuid'] ?? null;
        $this->id           = $data['id'] ?? null;
        $this->parent_uuid  = $data['parent_uuid'] ?? '';
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
            'parent_uuid'   => $this->parent_uuid,
            'name'          => $this->name,
            'description'   => $this->description,
            'is_public'     => $this->is_public,
            'user_id'       => $this->user_id,
        ];
    }
}
