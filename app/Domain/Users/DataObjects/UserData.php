<?php

namespace App\Domains\Users\DataObjects;

class UserData
{
    public string $name;

    public string $email;

    public ?int $id;

    public ?int $collection_count;

    public ?int $folder_count;

    public function __construct(array $data)
    {
        $this->name             = $data['name'] ?? '';
        $this->email            = $data['email'] ?? '';
        $this->id               = $data['id'] ?? null;
        $this->collection_count = $data['collection_count'] ?? null;
        $this->folder_count     = $data['folder_count'] ?? null;
    }

    public function toArray()
    {
        return [
            'name'              => $this->name,
            'email'             => $this->email,
            'id'                => $this->id,
            'collection_count'  => $this->collection_count,
            'folder_count'      => $this->folder_count,
        ];
    }
}