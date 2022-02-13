<?php

namespace Tests\Feature\Http\Controllers;

use App\Actions\NormalizeString;
use App\Domain\Cards\Models\Card;
use App\Domain\Sets\Models\Set;
use Tests\Feature\Domain\CardCollectionTestCase;

class CardSearchControllerTest extends CardCollectionTestCase
{
//    /** @test */
//    public function invalid_search_returns_no_results() : void
//    {
//        Set::factory()
//            ->has(Card::factory()->count(3))
//            ->count(3)
//            ->create();
//
//        $response = $this->post(route('cards-search.store'), [
//            'card'  => 'test',
//        ]);
//
//        $this->assertEmpty($response['data']);
//
//        $response = $this->post(route('cards-search.store'), [
//            'set'  => 'test',
//        ]);
//
//        $this->assertEmpty($response['data']);
//    }
//
//    /** @test */
//    public function search_request_returns_list_of_cards() : void
//    {
//        Set::factory()
//            ->has(Card::factory()->count(3))
//            ->count(3)
//            ->create();
//
//        $setName  = 'Set Test';
//        $cardName = 'Card Test';
//
//        Set::factory()
//            ->has(
//                Card::factory()
//                    ->state(function () use ($cardName) {
//                        return [
//                            'name'            => $cardName,
//                            'name_normalized' => (new NormalizeString)($cardName),
//                        ];
//                    })
//            )
//            ->create(['name' => $setName]);
//
//        $response = $this->post(route('cards-search.store'), [
//            'card'  => 'test',
//        ]);
//
////        $this->assertCount(1, $response['data']);
////
////        $response = $this->post(route('cards-search.store'), [
////            'set'  => 'test'
////        ]);
////
////        $this->assertCount(1, $response['data']);
//    }
}
