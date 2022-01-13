<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;
use App\Domain\Prices\Aggregate\DataObjects\SummaryData;
use App\Domains\Users\DataObjects\UserData;

class CollectionData implements DataObjectInterface
{
    public array $allowed;

    public string $description;

    public ?string $folder_uuid;

    public array $groups;

    public ?int $id;

    public bool $is_public;

    public string $name;

    public ?SummaryData $summary_data;

    public ?UserData $user;

    public ?int $user_id;

    public ?string $uuid;

    public function __construct(array $data)
    {
        $this->allowed      = $data['allowed'] ?? [];
        $this->description  = $data['description'] ?? '';
        $this->folder_uuid  = $data['folder_uuid'] ?? null;
        $this->groups       = $data['groups'] ?? [];
        $this->id           = $data['id'] ?? null;
        $this->is_public    = $data['is_public'] ?? false;
        $this->name         = $data['name'] ?? '';
        $this->summary_data = $data['summary_data'] ?? null;
        $this->user         = $data['user'] ?? null;
        $this->user_id      = $data['user_id'] ?? null;
        $this->uuid         = $data['uuid'] ?? null;
    }

    public function toArray() : array
    {
        return [
            'allowed'       => $this->allowed,
            'description'   => $this->description,
            'folder_uuid'   => $this->folder_uuid,
            'groups'        => $this->groups,
            'id'            => $this->id,
            'is_public'     => $this->is_public,
            'name'          => $this->name,
            'summary_data'  => $this->summary_data,
            'user'          => $this->user,
            'user_id'       => $this->user_id,
            'uuid'          => $this->uuid,
        ];
    }
}
