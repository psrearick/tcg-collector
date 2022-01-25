<?php

namespace Tests\Feature\Domain;

use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Aggregate\Actions\DeleteFolder;
use App\Domain\Folders\Aggregate\Actions\UpdateFolder;
use App\Domain\Folders\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolderTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_folder_can_be_created()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Folder',
            'name'          => 'description 01',
        ];

        $uuid = (new CreateFolder)($params);

        $this->assertEquals($params['name'], Folder::uuid($uuid)->name);
    }

    public function test_a_folder_can_be_deleted()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Folder',
            'name'          => 'description 01',
            'is_public'     => false,
        ];

        $uuid   = (new CreateFolder)($params);
        $folder = Folder::uuid($uuid);

        (new DeleteFolder)($uuid);
        $this->assertSoftDeleted($folder);
    }

    public function test_a_folder_can_be_updated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Folder',
            'name'          => 'description 01',
            'is_public'     => false,
        ];

        $uuid   = (new CreateFolder)($params);
        $folder = Folder::uuid($uuid);

        $params = [
            'description'   => 'New Folder Updated',
            'name'          => 'description 02',
            'uuid'          => $uuid,
            'is_public'     => true,
        ];

        (new UpdateFolder)($params);

        $folder = $folder->refresh();
        $this->assertEquals($params['uuid'], $folder->uuid);
        $this->assertEquals($params['name'], $folder->name);
        $this->assertEquals($params['description'], $folder->description);
        $this->assertEquals($params['is_public'], $folder->is_public);
    }
}
