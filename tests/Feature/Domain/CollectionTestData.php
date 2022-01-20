<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Domain\Folders\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection as SupportCollection;
use Tests\Feature\Domain\Traits\WithCollectionCards;

class CollectionTestData
{
    use WithCollectionCards, WithFaker;

    public ?SupportCollection $collections;

    public ?Folder $folder;

    public ?SupportCollection $folders;

    public ?CollectionTestData $parent;

    public ?User $user = null;

    public function __construct(?Folder $folder = null)
    {
        $this->folder       = $folder;
        $this->folders      = new SupportCollection();
        $this->collections  = new SupportCollection();

        if ($folder) {
            $this->user = $folder->user;
        }

        $this->setUpFaker();
    }

    public function addCollections($count = 1) : self
    {
        $uuid = optional($this->folder)->uuid ?: '';

        for ($c = 0; $c < $count; $c++) {
            $collection = $this->createCollection($uuid);
            $this->collections->push(Collection::uuid($collection));
        }

        return $this;
    }

    public function addFolders($count = 1) : self
    {
        for ($c = 0; $c < $count; $c++) {
            $new = $this->new();
            $this->folders->push($this->folder ? $new->init($this) : $new->init());
        }

        return $this;
    }

    public function getCollection() : Collection
    {
        return $this->collections->first();
    }

    public function init(?CollectionTestData $parent = null) : self
    {
        if (!$this->folder) {
            $parentUuid     = $parent ? $parent->folder->uuid : '';
            $folder         = $this->createFolder($this->faker->words(3, true), $parentUuid);
            $this->folder   = Folder::uuid($folder);
        }

        $this->parent = $parent;

        return $this;
    }

    public function move(string $uuid = '', ?int $userId = null) : self
    {
        if (!$this->folder) {
            return $this;
        }

        if (!$userId) {
            $userId = optional($this->user)->id;
        }

        if (!$userId) {
            $userId = $this->folder->user->id;
        }

        if (!$userId) {
            return $this;
        }

        (new MoveFolder)($this->folder->uuid, $uuid, $userId);

        $remove = null;
        if ($this->parent) {
            $remove = $this->parent->folders->filter(
                fn ($folder) => $folder->folder->uuid = $this->folder->uuid
            )->keys()->first();
        }

        if ($remove !== null) {
            $this->parent->folders->forget($remove);
        }

        return $this;
    }

    public function new() : CollectionTestData
    {
        return new self;
    }

    public function refresh() : self
    {
        $this->folder->refresh();

        foreach ($this->folders as $folder) {
            $folder->refresh();
        }

        foreach ($this->collections as $collection) {
            $collection->refresh();
        }

        return $this;
    }
}
