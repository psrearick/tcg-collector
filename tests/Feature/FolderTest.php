<?php

namespace Tests\Feature;

use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class FolderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_folder_can_be_created()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Folder',
            'name'          => 'description 01',
        ];

        $uuid = (new CreateFolder)($params);

        $this->assertEquals($params['name'], Folder::uuid($uuid)->name);
    }
}
