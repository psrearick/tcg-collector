<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Aggregate\Actions\MoveCollection;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Domain\Folders\Models\Folder;

class AllowedDestinationsTest extends CardCollectionTestCase
{
    public function test_a_collection_can_move_to_root() : void
    {
        $root = new CollectionTestData();
        $root->init()->addCollections();

        $collection = $root->collections->first();

        $this->assertEquals($root->folder->uuid, $collection->folder_uuid);

        (new MoveCollection)($collection->uuid, '', $this->user->id);

        $collection->refresh();

        $this->assertEmpty($collection->folder_uuid);
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

    public function test_a_folder_can_move_to_root() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $root->folders->each(fn ($folder) => $folder->addFolders(2));

        $firstFolderToMove = $root->folders->last()->folders->last();

        $this->assertNotEmpty($firstFolderToMove->folder->parent_uuid);

        (new MoveFolder)($firstFolderToMove->folder->uuid, '', $this->user->id);

        $firstFolderToMove->refresh();

        $this->assertEmpty($firstFolderToMove->folder->parent_uuid);
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

    // public function test_a_collection_can_move_to_root_folder() : void
    // {}

    // public function test_a_folder_can_move_to_an_ancestor_folder()
    // {}

    // public function test_a_folder_can_move_to_into_a_parallel_tree()
    // {}

    // public function test_a_collection_can_move_to_into_a_parallel_tree()
    // {}

    // public function test_a_moved_folder_is_a_valid_destination()
    // {}

    // public function test_a_moved_folders_descendants_are_valid_destinations()
    // {}
}
