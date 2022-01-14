<?php

namespace Tests\Feature\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Domain\Folders\Models\Folder;

class FolderFolderTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_folder_can_be_created_in_a_folder()
    {
        $folderFolderCreated    = $this->createFolderInFolder();
        $folderUuid             = $folderFolderCreated['folder_uuid'];
        $parentUuid             = $folderFolderCreated['parent_uuid'];
        $folder                 = Folder::uuid($folderUuid);
        $parent                 = Folder::uuid($parentUuid);
        $folderParent           = $folder->parent;
        $parentDescendants      = $parent->descendants;

        $this->assertModelExists($folderParent);
        $this->assertEquals($parentUuid, $folderParent->uuid);
        $this->assertEquals(1, $parentDescendants->count());
        
        $descendant = $parentDescendants->first();
        $this->assertEquals($folderUuid, $descendant->uuid);
    }

    public function test_a_folder_can_change_folders()
    {
        $folderFolderCreated    = $this->createFolderInFolder();
        $folderUuid             = $folderFolderCreated['folder_uuid'];
        $parentUuid             = $folderFolderCreated['parent_uuid'];
        $userId                 = $folderFolderCreated['user_id'];

        $params = [
            'name'  => 'parent folder 2'
        ];

        $newFolderUuid = (new CreateFolder)($params);

        (new MoveFolder)($folderUuid, $newFolderUuid, $userId);

        $childFolder    = Folder::uuid($folderUuid);
        $parentFolder   = Folder::uuid($newFolderUuid);
        $originalFolder = Folder::uuid($parentUuid);

        $childParent        = $childFolder->parent;
        $parentChildren     = $parentFolder->children;
        $originalChildren   = $originalFolder->children;
        $parentChild        = $parentChildren->first();

        $this->assertEquals(0, $originalChildren->count());
        $this->assertModelExists($childParent);
        $this->assertModelExists($parentChild);
        $this->assertEquals($folderUuid, $parentChild->uuid);
        $this->assertEquals($newFolderUuid, $childParent->uuid);
    }

    public function test_a_folder_can_move_from_root_to_a_folder()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $params = [
            'name'          => 'child folder',
            'user_id'       => $user->id,
        ];

        $child_uuid = (new CreateFolder)($params);

        $params = [
            'name'  => 'parent folder'
        ];

        $parent_uuid = (new CreateFolder)($params);

        (new MoveFolder)($child_uuid, $parent_uuid, $user->id);

        $childFolder    = Folder::uuid($child_uuid);
        $parentFolder   = Folder::uuid($parent_uuid);

        $childParent        = $childFolder->parent;
        $parentChildren     = $parentFolder->children;
        $parentChild        = $parentChildren->first();

        $this->assertModelExists($childParent);
        $this->assertModelExists($parentChild);
        $this->assertEquals($child_uuid, $parentChild->uuid);
        $this->assertEquals($parent_uuid, $childParent->uuid);
    }

    public function test_a_folder_can_move_from_a_folder_to_root()
    {
        $folderFolderCreated    = $this->createFolderInFolder();
        $folderUuid             = $folderFolderCreated['folder_uuid'];
        $parentUuid             = $folderFolderCreated['parent_uuid'];
        $userId                 = $folderFolderCreated['user_id'];

        (new MoveFolder)($folderUuid, '', $userId);

        $childFolder    = Folder::uuid($folderUuid);
        $originalFolder = Folder::uuid($parentUuid);

        $childParent        = $childFolder->parent;
        $originalChildren   = $originalFolder->children;

        $this->assertEquals(0, $originalChildren->count());
        $this->assertNull($childParent);
    }

    private function createFolderInFolder() : array
    {

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $params = [
            'name'          => 'parent folder',
            'user_id'       => $user->id,
        ];

        $parent_uuid = (new CreateFolder)($params);

        $params = [
            'name'          => 'child folder',
            'user_id'       => $user->id,
            'parent_uuid'   => $parent_uuid,
        ];

        $folder_uuid = (new CreateFolder)($params);

        return [
            'folder_uuid'   => $folder_uuid,
            'parent_uuid'   => $parent_uuid,
            'user_id'       => $user->id,
        ];
    }
}
