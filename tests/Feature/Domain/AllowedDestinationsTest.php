<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Domain\Folders\Models\AllowedDestination;
use App\Domain\Folders\Models\Folder;

class AllowedDestinationsTest extends CardCollectionTestCase
{
    public function test_a_collection_can_move_to_into_a_parallel_tree()
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));

        $subjectFolder      = $root->followTree([0, 0]);
        $destination        = $root->followTree([1, 0]);
        $destinationUuid    = $destination->folder->uuid;

        $subjectFolder->addCollections();

        $subject = $subjectFolder->getCollection();

        $this->assertContains(
            $destinationUuid,
            $subject->allowedDestinations->pluck('destination')
        );

        $subjectFolder->MoveCollection($subject->uuid, $destinationUuid);
        $subject->refresh();

        $this->assertNotContains(
            $destinationUuid,
            $subject->allowedDestinations->pluck('destination')
        );

        $this->assertEquals($destinationUuid, $subject->folder->uuid);
    }

    public function test_a_collection_can_move_to_root() : void
    {
        $root = new CollectionTestData();
        $root->init()->addCollections();

        $collection = $root->getCollection();

        $this->assertModelExists($collection->folder);

        $root->MoveCollection();

        $collection->refresh();
        $root->refresh();

        $this->assertEmpty($root->collections);
        $this->assertEmpty($collection->folder);
    }

    public function test_a_folder_can_move_to_a_folder_in_the_root()
    {
        $root = new CollectionTestData(null, $this->user);
        $root->init()->addFolders();

        $this->assertCount(1, $root->folders);
        $this->assertEmpty($root->folder->parent);

        $folder = $root->folders->first();

        $folder->move();
        $folder->refresh();

        $this->assertCount(0, $root->folders);
        $this->assertCount(0, $root->folder->children);
        $this->assertEmpty($folder->folder->parent);

        $root->move($folder->folder->uuid);
        $root->refresh();

        $this->assertCount(1, $folder->folder->children);
        $this->assertNotEmpty($root->folder->parent);
    }

    public function test_a_folder_can_move_to_an_ancestor_folder()
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));
        $root->refresh();

        $a   = $root;
        $a1  = $root->followTree([0]);
        $a11 = $root->followTree([0, 0]);
        $a12 = $root->followTree([0, 1]);
        $a2  = $root->followTree([1]);
        $a21 = $root->followTree([1, 0]);
        $a22 = $root->followTree([1, 1]);

        $destinationMap = [
            $a->uuid()   => [],
            $a1->uuid()  => [$a2->uuid(), $a22->uuid(), $a21->uuid()],
            $a11->uuid() => [$a->uuid(), $a12->uuid(), $a2->uuid(), $a21->uuid(), $a22->uuid()],
            $a12->uuid() => [$a->uuid(), $a11->uuid(), $a2->uuid(), $a21->uuid(), $a22->uuid()],
            $a2->uuid()  => [$a1->uuid(), $a11->uuid(), $a12->uuid()],
            $a21->uuid() => [$a->uuid(), $a11->uuid(), $a12->uuid(), $a1->uuid(), $a22->uuid()],
            $a22->uuid() => [$a->uuid(), $a11->uuid(), $a12->uuid(), $a1->uuid(), $a21->uuid()],
        ];

        foreach ($destinationMap as $folder => $destinations) {
            $validDestinations = AllowedDestination::where('uuid', '=', $folder)
                ->where('type', '=', 'folder')
                ->pluck('destination')
                ->toArray();

            $this->assertCount(count($destinations), $validDestinations);

            foreach ($destinations as $destination) {
                $this->assertContains($destination, $validDestinations);
            }
        }

        /**
         * @var CollectionTestData $subFolder
         */
        $subFolder = $root->folders->first()->folders->first();

        $this->assertContains(
            $root->uuid(),
            $subFolder->folder->allowedDestinations->pluck('destination')
        );

        $subFolder->move($root->uuid());

        $subFolder->refresh();

        $this->assertEquals($root->uuid(), $subFolder->folder->parent->uuid);
        $this->assertEquals($root->uuid(), $subFolder->parent->uuid());

        $this->assertNotContains(
            $root->uuid(),
            $subFolder->folder->allowedDestinations->pluck('destination')
        );
    }

    public function test_a_folder_can_move_to_into_a_parallel_tree()
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));

        $subject            = $root->followTree([0, 0]);
        $destination        = $root->followTree([1, 0]);
        $destinationUuid    = $destination->folder->uuid;

        $this->assertContains(
            $destinationUuid,
            $subject->folder->allowedDestinations->pluck('destination')
        );

        $subject->move($destinationUuid);
        $subject->refresh();

        $this->assertNotContains(
            $destinationUuid,
            $subject->folder->allowedDestinations->pluck('destination')
        );

        $this->assertEquals($destinationUuid, $subject->folder->parent->uuid);
    }

    public function test_a_folder_can_move_to_root() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));

        /**
         * @var CollectionTestData $folderToMove
         */
        $folderToMove       = $root->folders->last()->folders->last();
        $parentUuid         = $folderToMove->folder->parent_uuid;

        $this->assertNotEmpty($parentUuid);
        $this->assertNotContains(
            $parentUuid,
            $folderToMove->folder->allowedDestinations->pluck('destination')
        );

        (new MoveFolder)($folderToMove->folder->uuid, '', $this->user->id);

        $folderToMove->refresh();

        $this->assertEmpty($folderToMove->folder->parent_uuid);
        $this->assertContains(
            $parentUuid,
            $folderToMove->folder->allowedDestinations->pluck('destination')
        );
    }

    public function test_a_folder_cannot_move_into_its_descendants() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));
        $root->refresh();

        $this->assertEmpty($root->folder->allowedDestinations);
        $this->assertCount(6, $root->folder->descendants);
    }

    public function test_a_folder_is_an_invalid_destination_for_its_children() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders();

        $this->assertEmpty($root->folders->first()->folder->allowedDestinations);
    }

    public function test_a_folder_is_an_invalid_destination_for_its_collections() : void
    {
        $root = new CollectionTestData();
        $root->init();

        $collection     = $root->addCollections()->getCollection();
        $destinations   = $collection->allowedDestinations
            ->where('folder_uuid', '=', $root->folder->uuid)
            ->all();

        $this->assertNotEmpty($root->folder->uuid);
        $this->assertEquals($root->folder->uuid, $collection->folder_uuid);
        $this->assertEmpty($destinations);
    }

    public function test_a_moved_folder_is_a_valid_destination()
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));

        $subject            = $root->followTree([0, 0]);
        $subjectUuid        = $subject->folder->uuid;
        $destination        = $root->followTree([1, 0]);
        $destinationUuid    = $destination->folder->uuid;
        $check              = $root->followTree([0]);

        $this->assertNotContains(
            $subject->folder->uuid,
            $check->folder->allowedDestinations->pluck('destination')
        );

        $subject->move($destinationUuid);
        $root->refresh()->rebuildTree([1, 0]);

        $this->assertContains(
            $subjectUuid,
            $check->folder->allowedDestinations->pluck('destination')
        );

        $check->move($subjectUuid);
        $root->refresh()->rebuildFolder($root);
        $check->refresh();

        $this->assertNotContains(
            $subjectUuid,
            $check->folder->allowedDestinations->pluck('destination')
        );

        $checkBack  = $root->followTree([0, 0, 0]); // subject
        $checkBack->move($root->folder->uuid);
        $root->refresh()->rebuildFolder($root);
        $checkBack->refresh();

        $this->assertNotContains(
            $root->folder->uuid,
            $checkBack->folder->allowedDestinations->pluck('destination')
        );
    }

    public function test_a_new_collection_among_folders_has_destinations() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $collection = $root->addCollections()->getCollection();

        $this->assertCount(2, $collection->allowedDestinations);
    }

    public function test_a_new_folder_among_folders_has_destination() : void
    {
        $root = new CollectionTestData();
        $root->addFolders(2);

        $this->assertCount(1, $root->folders->last()->folder->allowedDestinations);
    }

    public function test_a_new_folder_is_a_valid_destination_for_an_existing_collection() : void
    {
        $root       = new CollectionTestData();
        $collection = $root
            ->init()
            ->addCollections()
            ->getCollection();
        $root->addFolders()->refresh();

        $this->assertCount(1, $collection->allowedDestinations);
    }

    public function test_a_new_folder_is_a_valid_destination_for_an_existing_collection_in_a_different_tree() : void
    {
        $root       = new CollectionTestData();
        $root
            ->addCollections()
            ->addFolders()
            ->getCollection();

        $folder     = Folder::where('user_id', '=', $this->user->id)->first();
        $collection = Collection::where('user_id', '=', $this->user->id)->first();

        $this->assertCount(1, $collection->allowedDestinations);
        $this->assertEquals($folder->uuid,
            $collection->allowedDestinations->first()->destination);
    }

    public function test_a_new_folder_is_a_valid_destination_for_an_existing_folder() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);

        $this->assertCount(1, $root->folders->first()->folder->allowedDestinations);
    }

    public function test_a_single_collection_has_no_destinations() : void
    {
        $root       = new CollectionTestData();
        $collection = $root->addCollections()->getCollection();

        $this->assertEmpty($collection->allowedDestinations);
    }

    public function test_a_single_folder_has_no_destinations() : void
    {
        $root = new CollectionTestData();
        $root->init();

        $this->assertEmpty($root->folder->allowedDestinations);
    }
}
