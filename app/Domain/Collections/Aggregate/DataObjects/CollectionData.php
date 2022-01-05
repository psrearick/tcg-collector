<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\Domain\Prices\Aggregate\DataObjects\SummaryData;
use App\Domains\Users\DataObjects\UserData;

class CollectionData
{
    public string $description;

    public ?string $folder_uuid;

    public ?int $id;

    public bool $is_public;

    public string $name;

    public ?int $user_id;

    public ?string $uuid;

    public array $groups;

    public ?UserData $user;

    public ?SummaryData $summary_data;

    public function __construct(array $data)
    {
        $this->uuid         = $data['uuid'] ?? null;
        $this->id           = $data['id'] ?? null;
        $this->folder_uuid  = $data['folder_uuid'] ?? null;
        $this->name         = $data['name'] ?? '';
        $this->description  = $data['description'] ?? '';
        $this->is_public    = $data['is_public'] ?? false;
        $this->user_id      = $data['user_id'] ?? null;
        $this->groups       = $data['groups'] ?? [];
        $this->user         = $data['user'] ?? null;
        $this->summary_data = $data['summary_data'] ?? null;
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
            'groups'        => $this->groups,
            'user'          => $this->user,
            'summary_data'  => $this->summary_data,
        ];
    }
}
