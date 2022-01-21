<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Aggregate\Actions\MoveCollection;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Domain\Folders\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection as SupportCollection;
use Tests\Feature\Domain\Traits\WithCollectionCards;
use App\Domain\Cards\Models\Card;

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

    public function addCard($uuid, $index) : self
    {
        if (!$uuid) {
            return $this;
        }

        $this->createCollectionCard($uuid, $index);

        return $this;
    }

    public function addCards($count = 1, ?string $uuid = '') : self
    {
        $collection = $uuid ?: optional($this->collections->first())->uuid;
        
        if (!$collection) {
            return $this;
        }

        $cardCount = Card::count();

        for ($c = 0; $c < $count; $c++) {
            $index = $c < $cardCount ? $c : $cardCount - 1;
            $this->addCard($collection, $index);
        }

        return $this;
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

    public function followTree(array $tree) : ?self
    {
        $key = array_shift($tree);

        /**
         * @var self $record
         */
        $record = $this->folders->get($key);

        if (!$tree) {
            return $record;
        }

        return $record->followTree($tree);
    }

    public function getCollection($index = 0) : Collection
    {
        return $this->collections->get($index);
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

        if (!$userId = $this->moveUserId($userId)) {
            return $this;
        }

        (new MoveFolder)($this->folder->uuid, $uuid, $userId);

        $this->removeFromParentFolders();

        $this->moveToNewParent($uuid);

        $this->updateNewParentWithFolder();

        return $this;
    }

    public function MoveCollection(string $uuid = '', string $destination = '', ?int $userId = null) : self
    {
        if (!$uuid = $this->moveUuid($uuid)) {
            return $this;
        }

        if (!$userId = $this->moveUserId($userId)) {
            return $this;
        }

        (new MoveCollection)($uuid, $destination, $userId);

        $this->removeCollectionFromCollections($uuid);

        return $this;
    }

    public function new() : CollectionTestData
    {
        return new self;
    }

    public function rebuildFolder(CollectionTestData $data) : CollectionTestData
    {
        $folder         = $data->folder;
        $parent         = $folder->parent;
        $children       = $folder->children;
        $collections    = $folder->collections;

        $data->parent       = new CollectionTestData($parent);
        $data->collections  = $collections;
        $data->folders      = $this->rebuildFolders($children);

        return $data;
    }

    public function rebuildFolders(SupportCollection $folders) : SupportCollection
    {
        $folderCollection = new SupportCollection();

        $folders->each(function (Folder $folder) use (&$folderCollection) {
            $data = new CollectionTestData($folder);
            $folderCollection->push($this->rebuildFolder($data));
        });

        return $folderCollection;
    }

    public function rebuildTree(array $tree) : self
    {
        $toUpdate   = $this->followTree($tree);

        $this->rebuildFolder($toUpdate);

        return $this;
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

    public function uuid() : string
    {
        if (!$this->folder) {
            return '';
        }

        return $this->folder->uuid;
    }

    protected function moveToNewParent(?string $uuid = null) : void
    {
        $newParent  = $uuid ? Folder::uuid($uuid) : null;
        $parentData = new CollectionTestData($newParent);

        $this->init($parentData);
    }

    protected function moveUserId(?int $userId = null) : ?int
    {
        if (!$userId) {
            $userId = optional($this->user)->id;
        }

        if (!$userId) {
            $userId = $this->folder->user->id;
        }

        if (!$userId) {
            return null;
        }

        return $userId;
    }

    protected function moveUuid(?string $uuid = null) : ?string
    {
        $uuid = $uuid ?: optional($this->getCollection())->uuid;

        if (!$uuid) {
            return null;
        }

        return $uuid;
    }

    protected function removeCollectionFromCollections(string $uuid) : void
    {
        $remove = $this->collections
            ->filter(fn ($collection) => $collection->uuid == $uuid)
            ->keys()
            ->first();

        if ($remove !== null) {
            $this->collections->forget($remove);
        }
    }

    protected function removeFromParentFolders() : void
    {
        $remove = null;
        if ($this->parent) {
            $remove = $this->parent->folders->filter(
                fn ($folder) => $folder->folder->uuid = $this->folder->uuid
            )->keys()->first();
        }

        if ($remove !== null) {
            $this->parent->folders->forget($remove);
        }
    }

    protected function updateNewParentWithFolder() : void
    {
        if (!$parent = $this->parent) {
            return;
        }

        $parent->folders->push($this);
    }
}
