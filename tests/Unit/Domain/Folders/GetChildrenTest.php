<?php

namespace Tests\Unit\Domain\Folders;

use App\Domain\Folders\Aggregate\Actions\GetChildren;
use Tests\Feature\Domain\CardCollectionTestCase;
use Tests\Feature\Domain\CollectionTestData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;

/**
 * @see GetChildren
 */
class GetChildrenTest extends CardCollectionTestCase
{
    private GetChildren $getChildren;

    private array $folders;

    private array $destinationMap;

    public function setUp() : void
    {
        parent::setUp();

        $this->getChildren = new GetChildren();

        // set up database with folders, collections, and items
        $this->initialize();
    }

    /**
     * @test
     */
    public function invokeForFolderInRoot() : void
    {
        /** @var string $collection */
        foreach ($this->destinationMap as $folderUuid => $map) {
            $result = ($this->getChildren)($folderUuid, $this->user->id);

            $resultFolders          = $result['folders'];
            $childrenFolderUuids    = Folder::where('parent_uuid', '=', $folderUuid)->pluck('uuid')->toArray();
            $resultFolderUuids      = $resultFolders->pluck('uuid')->toArray();

            $this->assertCount(count($childrenFolderUuids), $resultFolders);
            $this->assertEqualsCanonicalizing($childrenFolderUuids, $resultFolderUuids);

            foreach ($resultFolders as $resultFolder) {
                $childAllowed = collect($resultFolder['allowed']);
                $allowed = $childAllowed->map(fn ($allowed) => $allowed['uuid'] ?: $allowed['name']);
                $this->assertEqualsCanonicalizing($this->destinationMap[$resultFolder['uuid']], $allowed->toArray());
            }

            $resultCollections      = $result['collections'];
            $childCollectionUuids   = Collection::where('folder_uuid', '=', $folderUuid)->pluck('uuid')->toArray();
            $resultCollectionUuids  = $result['collections']->pluck('uuid')->toArray();

            $this->assertCount(count($childCollectionUuids), $resultCollections);
            $this->assertEqualsCanonicalizing($childCollectionUuids, $resultCollectionUuids);

            foreach ($resultCollections as $resultCollection) {
                $childAllowed   = collect($resultCollection['allowed']);
                $allowed        = $childAllowed->map(fn ($allowed) => $allowed['uuid'] ?: $allowed['name']);
                $this->assertContains('Root', $allowed);
                $this->assertNotContains($folderUuid, $allowed);
            }
        }
    }

    /**
     * @test
     */
    public function invokeForRoot() : void
    {
        $result = ($this->getChildren)(null, $this->user->id);

        collect($result['folders'])->each(fn ($folder) => 
            $this->assertEqualsCanonicalizing(
                $this->destinationMap[$folder['uuid']],
                    collect($folder['allowed'])->pluck('uuid')->toArray()
            )
        );

        collect($result['collections'])->each(fn ($collection) =>
            $this->assertCount(6, $collection['allowed']) &&
                $this->assertNotContains(
                    'Root', collect($collection['allowed'])->pluck('name')->toArray()
                )
        );
    }

    protected function initialize() : void
    {
        $root = new CollectionTestData();
        $root->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));

        $root->addCollections(3);
        $root->collections->each(
            fn ($collection) => $root->addCards(5, $collection->uuid)
        );

        $root->refresh();

        $a1  = $root->followTree([0]);
        $a11 = $root->followTree([0, 0]);
        $a12 = $root->followTree([0, 1]);
        $a2  = $root->followTree([1]);
        $a21 = $root->followTree([1, 0]);
        $a22 = $root->followTree([1, 1]);

        $this->destinationMap = [
            $a1->uuid()  => [$a2->uuid(), $a22->uuid(), $a21->uuid()],
            $a11->uuid() => ['Root', $a12->uuid(), $a2->uuid(), $a21->uuid(), $a22->uuid()],
            $a12->uuid() => ['Root', $a11->uuid(), $a2->uuid(), $a21->uuid(), $a22->uuid()],
            $a2->uuid()  => [$a1->uuid(), $a11->uuid(), $a12->uuid()],
            $a21->uuid() => ['Root', $a11->uuid(), $a12->uuid(), $a1->uuid(), $a22->uuid()],
            $a22->uuid() => ['Root', $a11->uuid(), $a12->uuid(), $a1->uuid(), $a21->uuid()],
        ];

        $this->folders = [
            $a1->uuid()  => $a1,
            $a11->uuid() => $a11,
            $a12->uuid() => $a12,
            $a2->uuid()  => $a2,
            $a21->uuid() => $a21,
            $a22->uuid() => $a22,
        ];

        /** @var CollectionTestData $folder */
        foreach ($this->folders as $folder) {
            $folder->addCollections(3);
            $folder->refresh();

            /** @var Collection $collection */
            foreach ($folder->collections as $collection) {
                $folder->addCards(5, $collection->uuid);
            }
        }

        $root->refresh();
    }
}