<?php

namespace Tests\Feature\Http\Controllers;

use App\Domain\Collections\Models\Collection;
use Tests\Feature\Domain\CardCollectionTestCase;
use Inertia\Testing\Assert;
use Tests\Feature\Domain\CollectionTestData;

/**
 * @see \App\Http\Controllers\CollectionsController
 */
class CollectionsControllerTest extends CardCollectionTestCase
{
    /**
     * @test
     */
    public function create_returns_an_ok_response()
    {
        $response = $this->get(route('collections.create'));

        $response->assertOk();
    }

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
        $this->act();

        $collectionUuid = $this->createCollection();
        $collection     = Collection::uuid($collectionUuid);

        $response = $this->delete(route('collections.destroy', ['collection' => $collectionUuid]));

        $response->assertRedirect();
        $this->assertSoftDeleted($collection);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
        $collection         = $this->createCollection();
        $collectionModel    = Collection::uuid($collection);
        
        $response = $this->get(route('collections.edit', ['collection' => $collection]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
                ->component('Collections/Edit')
                ->has('collection', fn (Assert $page) => $page
                    ->where('uuid', $collection)
                    ->where('name', $collectionModel->name)
                    ->where('description', $collectionModel->description)
                    ->etc()
                )
        );
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $response = $this->get(route('collections.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Collections/Index')
            ->has('collections')
            ->has('folders')
            ->has('totals')
        );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
        $collection         = $this->createCollection();
        $collectionModel    = Collection::uuid($collection);
        
        $response = $this->get(route('collections.show', ['collection' => $collection]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
                ->component('Collections/Show')
                ->has('collection', fn (Assert $page) => $page
                    ->where('uuid', $collection)
                    ->where('name', $collectionModel->name)
                    ->where('description', $collectionModel->description)
                    ->etc()
                )
        );
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $params = [
            'description'   => $this->faker->sentence(),
            'name'          => $this->faker->words(2, true),
            'is_public'     => false,
        ];
        $response = $this->post(route('collections.store'), $params);

        $response->assertRedirect();
        $this->assertCount(1, Collection::all());
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $collection         = $this->createCollection();

        $response = $this->put(route('collections.update', ['collection' => $collection]), [
            'uuid'          => $collection,
            'description'   => 'new description',
            'name'          => 'new name',
            'is_public'     => false,
        ]);

        $response->assertRedirect();

        $collectionModel    = Collection::uuid($collection);

        $this->assertEquals('new name', $collectionModel->name);
        $this->assertEquals('new description', $collectionModel->description);
    }
}
