<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use Illuminate\Support\Collection as SupportCollection;
use Tests\Feature\Domain\Traits\WithCollectionCards;
use Illuminate\Foundation\Testing\WithFaker;

class CollectionTestData
{
    use WithCollectionCards, WithFaker;

    public ?Folder $folder;

    public ?SupportCollection $folders;

    public ?SupportCollection $collections;

    public function __construct(?Folder $folder = null)
    {
        $this->folder       = $folder;
        $this->folders      = new SupportCollection();
        $this->collections  = new SupportCollection();

        $this->setUpFaker();
    }

    public function init(?Folder $parent = null) : self
    {
        if (!$this->folder) {
            $parentUuid     = $parent ? $parent->uuid : '';
            $folder         = $this->createFolder($this->faker->words(3, true), $parentUuid);
            $this->folder   = Folder::uuid($folder);
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
            $this->folders->push($this->folder 
                ? $new->init($this->folder) 
                : $new->init()
            );
        }

        return $this;
    }

    public function getCollection() : Collection
    {
        return $this->collections->first();
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