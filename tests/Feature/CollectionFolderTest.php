<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\MoveCollection;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Models\Folder;

class CollectionFolderTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_collection_can_be_created_in_a_folder()
    {
        $collectionFolderCreated    = $this->createCollectionInFolder();
        $folderUuid                 = $collectionFolderCreated['folder_uuid'];
        $collectionUuid             = $collectionFolderCreated['collection_uuid'];
        $folder                     = Folder::uuid($folderUuid);
        $collection                 = Collection::uuid($collectionUuid);
        $collectionFolder           = $collection->folder;
        $folderCollections          = $folder->collections();
        $folderCollection           = $folderCollections->first();
        
        $this->assertModelExists($collectionFolder);
        $this->assertEquals($folderUuid, $collectionFolder->uuid);
        $this->assertModelExists($folderCollection);
        $this->assertEquals($collectionUuid, $folderCollection->uuid);
    }

    public function test_a_collection_can_change_folders()
    {
        $collectionFolderCreated    = $this->createCollectionInFolder();
        $userId                     = $collectionFolderCreated['user_id'];
        $folderUuid                 = $collectionFolderCreated['folder_uuid'];
        $collectionUuid             = $collectionFolderCreated['collection_uuid'];

        $params = [
            'name'          => 'folder 02',
        ];

        $newFolderUuid = (new CreateFolder)($params);

        (new MoveCollection)($collectionUuid, $newFolderUuid, $userId);

        $folder                     = Folder::uuid($folderUuid);
        $collection                 = Collection::uuid($collectionUuid);
        $collectionFolder           = $collection->folder;
        $folderCollections          = $folder->collections();
        $newFolder                  = Folder::uuid($newFolderUuid);
        $newFolderCollections       = $newFolder->collections();
        $newFolderCollection        = optional($newFolderCollections)->first();

        $this->assertModelExists($newFolder);
        $this->assertModelExists($collectionFolder);
        $this->assertEquals(0, $folderCollections->count());
        $this->assertModelExists($newFolderCollection);
        $this->assertEquals($newFolderUuid, $collectionFolder->uuid);
        $this->assertEquals($collectionUuid, $newFolderCollection->uuid);
    }

    public function test_a_collection_can_move_from_root_to_a_folder()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $params = [
            'name'          => 'collection 01',
        ];

        $collectionUuid     = (new CreateCollection)($params);
        $collection         = Collection::uuid($collectionUuid);

        $this->assertNull($collection->folder);

        $params = [
            'name'          => 'folder 01',
        ];

        $folderUuid = (new CreateFolder)($params);

        (new MoveCollection)($collectionUuid, $folderUuid, $user->id);

        $folder     = Folder::uuid($folderUuid);
        $collection = $collection->refresh();

        $this->assertModelExists($collection->folder);
        $this->assertEquals($folderUuid, $collection->folder->uuid);
        $this->assertEquals(1, $folder->collections->count());
        $this->assertEquals($collectionUuid, $folder->collections->first()->uuid);
    }

    public function test_a_collection_can_move_from_a_folder_to_root()
    {
        $collectionFolderCreated    = $this->createCollectionInFolder();
        $userId                     = $collectionFolderCreated['user_id'];
        $folderUuid                 = $collectionFolderCreated['folder_uuid'];
        $collectionUuid             = $collectionFolderCreated['collection_uuid'];
        (new MoveCollection)($collectionUuid, '', $userId);

        $collection = Collection::uuid($collectionUuid);
        $folder     = Folder::uuid($folderUuid);
        $this->assertNull($collection->folder);
        $this->assertEquals(0, $folder->collections->count());
    }

    private function createCollectionInFolder() : array
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $params = [
            'name'          => 'folder 01',
        ];

        $folder_uuid = (new CreateFolder)($params);

        $params = [
            'name'          => 'collection 01',
            'folder_uuid'   => $folder_uuid,
        ];

        $collection_uuid    = (new CreateCollection)($params);

        return [
            'folder_uuid'       => $folder_uuid,
            'collection_uuid'   => $collection_uuid,
            'user_id'           => $user->id,
        ];
    }
}
