<?php

namespace Tests\Feature\Domain;

use App\Domain\Folders\Models\Folder;
use Tests\Feature\Domain\CardCollectionTestCase;
use Tests\Feature\Domain\CollectionTestData;

class AllowedDestinationsTest extends CardCollectionTestCase
{
    public function test_a_single_collection_has_no_destinations() : void
    {
        $root = new CollectionTestData();
        $collection = $root->addCollections()->getCollection();

        $this->assertEmpty($collection->allowedDestinations);
    }
    
    public function test_a_new_collection_among_folders_has_destinations() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);
        $collection = $root->addCollections()->getCollection();

        $this->assertCount(2, $collection->allowedDestinations);
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

    public function test_a_new_folder_is_a_valid_destination_for_an_existing_collection() : void
    {
        $root = new CollectionTestData();
        $collection = $root
            ->init()
            ->addCollections()
            ->getCollection();
        $root->addFolders()->refresh();

        $this->assertCount(1, $collection->allowedDestinations);
    }

    public function test_a_single_folder_has_no_destinations() : void
    {
        $root = new CollectionTestData();
        $root->init();

        $this->assertEmpty($root->folder->allowedDestinations);
    }

    public function test_a_new_folder_among_folders_has_destination() : void
    {
        $root = new CollectionTestData();
        $root->addFolders(2);

        $this->assertCount(1, $root->folders->last()->folder->allowedDestinations);
    }

    public function test_a_new_folder_is_a_valid_destination_for_an_existing_folder() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders(2);

        $this->assertCount(1, $root->folders->first()->folder->allowedDestinations);
    }

    public function test_a_folder_is_an_invalid_destination_for_its_children() : void
    {
        $root = new CollectionTestData();
        $root->init()->addFolders();

        $this->assertEmpty($root->folders->first()->folder->allowedDestinations);
    }
}