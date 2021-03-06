<?php

namespace Tests\Feature\Http\Controllers;

use Tests\Feature\Domain\CardCollectionTestCase;
use Tests\Feature\Domain\CollectionTestData;

/**
 * @see \App\Http\Controllers\CollectionsEditListSearchController
 */
class CollectionsEditListSearchControllerTest extends CardCollectionTestCase
{
    protected CollectionTestData $root;

    public function setUp() : void
    {
        parent::setUp();

        $root = new CollectionTestData();
        $root->init()->addCollections(2);
        $root->collections->pluck('uuid')->each(function ($uuid) use ($root) {
            $root->addCards(5, $uuid);
        });
        $this->root = $root;
    }

    public function invalidSearch() : array
    {
        return [
            [['card'  => 'invalid']],
            [['set' => 'invalid']],
        ];
    }

    /**
     * @test
     * @dataProvider invalidSearch
     */
    public function store_returns_an_empty_response_for_an_invalid_search(array $request) : void
    {
        $collection = $this->root->getCollection();

        $response = $this->post(route('collection-edit-list-search.store', ['collection' => $collection->uuid]),
            $request
        );

        $response->assertOk();
        $responseData = $response->json();

        foreach ($request as $field => $value) {
            $this->assertEquals($value, $responseData['search'][$field]);
        }

        $this->assertEquals(0, $responseData['totals']['total_cards']);
        $this->assertCount(0, $responseData['list']['data']);
        $this->assertEquals($collection->uuid, $responseData['collection']['uuid']);
    }

    /**
     * @test
     * @dataProvider validSearch
     */
    public function store_returns_an_ok_response(array $request) : void
    {
        $collection = $this->root->getCollection();

        $response = $this->post(route('collection-edit-list-search.store', ['collection' => $collection->uuid]),
            $request
        );

        $response->assertOk();
        $responseData = $response->json();

        foreach ($request as $field => $value) {
            $this->assertEquals($value, $responseData['search'][$field]);
        }

        $this->assertGreaterThan(0, $responseData['totals']['total_cards']);
        $this->assertGreaterThan(0, count($responseData['list']['data']));
        $this->assertEquals($collection->uuid, $responseData['collection']['uuid']);
    }

    public function validSearch() : array
    {
        return [
            [[]],
            [['card'  => '']],
            [['set' => '']],
            [['card'  => 'fury']],
            [['set' => 'tsp']],
            [['set' => 'time']],
        ];
    }
}
