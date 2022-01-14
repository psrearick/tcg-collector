<?php

namespace Tests\Feature;

use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\UpdateCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Domain\Collections\Models\Collection;
use App\Models\User;
use App\Domain\Collections\Aggregate\Actions\DeleteCollection;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a collection create request is made, an event is created
     *
     * @return void
     */
    public function test_a_collection_can_be_created()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Collection',
            'name'          => 'description 01',
            'is_public'     => false,
        ];

        $uuid = (new CreateCollection)($params);

        $collection = Collection::uuid($uuid);
        $this->assertEquals($params['name'], $collection->name);
        $this->assertEquals($params['description'], $collection->description);
        $this->assertEquals($params['is_public'], !!$collection->is_public);
    }

    public function test_a_collection_can_be_updated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Collection',
            'name'          => 'description 01',
            'is_public'     => false,
        ];

        $uuid       = (new CreateCollection)($params);
        $collection = Collection::uuid($uuid);
        $this->assertEquals($params['name'], $collection->name);

        $params = [
            'description'   => 'New Collection Updated',
            'name'          => 'description 02',
            'uuid'          => $uuid,
            'is_public'     => true,
        ];

        (new UpdateCollection)($params);

        $collection = $collection->refresh();
        $this->assertEquals($params['uuid'], $collection->uuid);
        $this->assertEquals($params['name'], $collection->name);
        $this->assertEquals($params['description'], $collection->description);
        $this->assertEquals($params['is_public'], $collection->is_public);
    }

    public function test_a_collection_can_be_deleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $params = [
            'description'   => 'New Collection',
            'name'          => 'description 01',
            'is_public'     => false,
        ];

        $uuid       = (new CreateCollection)($params);
        $collection = Collection::uuid($uuid);
        $this->assertEquals($params['name'], $collection->name);

        (new DeleteCollection)($uuid);
        $this->assertSoftDeleted($collection);
    }
}
