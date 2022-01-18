<?php

namespace Tests\Feature\Domain;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Models\User;
use Database\Seeders\CardsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Domain\Traits\WithCollectionCards;
use Tests\TestCase;

class CardCollectionSettingsTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithCollectionCards;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(CardsSeeder::class);
    }

    public function test_a_collection_card_can_change_acquired_price() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => true,
            'tracks_condition' => false,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // update to price to 80% of current
        // leave condition the same
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'NM',
            'oldCondition'  => 'NM',
            'newPrice'      => round($pivot['acquired_price'] * 0.8),
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // Original state is as expected
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals(
            $originalState['pivot']['acquired_price'], $originalState['collection']['acquired_value']
        );
        $this->assertEquals(
            $originalState['pivot']['acquired_price'], $originalState['collection']['acquired_value']
        );

        // the price price changed
        $this->assertEquals(
            $secondState['pivot']['acquired_price'],
            $secondState['collection_card_summary']['acquired_price']
        );

        // The new card updated the existing collection card summary instead of adding a new one
        $this->assertEquals(1, $secondState['collection_card_summaries']['total_cards']);

        // A new card_collections record was added
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);

        // card summary reflects price change
        $this->assertNotEquals(
            $originalState['collection_card_summary']['acquired_price'],
            $secondState['collection_card_summary']['acquired_price']
        );

        // collection summary reflects price change
        $this->assertNotEquals(
            $originalState['collection']['acquired_value'],
            $secondState['collection']['acquired_value'],
        );

        // folder summary reflects price change
        $this->assertNotEquals(
            $originalState['folder']['acquired_value'],
            $secondState['folder']['acquired_value'],
        );
    }

    public function test_a_collection_card_can_change_acquired_price_and_condition() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => true,
            'tracks_condition' => true,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // update to price to 80% of current
        // leave condition the same
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'NM',
            'oldCondition'  => 'NM',
            'newPrice'      => round($pivot['acquired_price'] * 0.8),
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the acquired_price change
        $secondState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'NM',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // Original state is as expected
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals(
            $originalState['pivot']['acquired_price'], $originalState['collection']['acquired_value']
        );
        $this->assertEquals(
            $originalState['pivot']['acquired_price'], $originalState['collection']['acquired_value']
        );
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);

        // the price price changed
        $this->assertEquals(
            $secondState['pivot']['acquired_price'],
            $secondState['collection_card_summary']['acquired_price']
        );

        // The new card updated the existing collection card summary instead of adding a new one
        $this->assertEquals(1, $secondState['collection_card_summaries']['total_cards']);

        // A new card_collections record was added
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);

        // card summary reflects price change
        $this->assertNotEquals(
            $originalState['collection_card_summary']['acquired_price'],
            $secondState['collection_card_summary']['acquired_price']
        );

        // collection summary reflects price change
        $this->assertNotEquals(
            $originalState['collection']['acquired_value'],
            $secondState['collection']['acquired_value'],
        );

        // folder summary reflects price change
        $this->assertNotEquals(
            $originalState['folder']['acquired_value'],
            $secondState['folder']['acquired_value'],
        );

        // Third State Assertions
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(1, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals('LP', $thirdState['collection_card_summary']['condition']);
    }

    public function test_a_collection_card_can_change_condition() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => false,
            'tracks_condition' => true,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'NM',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'NM',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);

        // Second State Assertions
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(1, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals('LP', $secondState['collection_card_summary']['condition']);

        // Third State Assertions
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(1, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $thirdState['collection_card_summary']['condition']);
    }

    public function test_a_collection_card_can_change_variants() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => false,
            'tracks_condition' => true,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // create new variant with condition of 'LP'
        // leave the price the same
        // set old condition to the same as new
        //   creates a new summary instead of updating one
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // change the condition to 'MP'
        // leave the price the same
        // setting the old condition
        //   to LP updates the LP vairant
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'MP',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);

        // Second State Assertions - new collection card and summary
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(2, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals('LP', $secondState['collection_card_summary']['condition']);

        // Third State Assertions - new collection card, no new summary
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(2, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals('MP', $thirdState['collection_card_summary']['condition']);
    }

    public function test_a_collection_card_can_delete_condition_variants() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => false,
            'tracks_condition' => true,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // create new variant with condition of 'LP'
        // leave the price the same
        // set old condition to the same as new
        //   creates a new summary instead of updating one
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // change the quantity of the
        // LP variant to 0
        // setting the old condition
        //   to LP updates the LP variant
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => -1,
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);

        // Second State Assertions - new collection card and summary
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(2, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals('LP', $secondState['collection_card_summary']['condition']);

        // Third State Assertions - one card removed
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(1, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);
    }

    public function test_a_collection_card_can_delete_price_variants() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => true,
            'tracks_condition' => false,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // create new variant with acquired_price of 80%
        // leave the condition the same
        // set old price to the same as new
        //   creates a new summary instead of updating one
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newPrice'      => round($pivot['acquired_price'] * 0.8),
            'oldPrice'      => round($pivot['acquired_price'] * 0.8),
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // change the condition to 'MP'
        // leave the price the same
        // setting the old condition
        //   to LP updates the LP vairant
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => -1,
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);

        // Second State Assertions - new collection card and summary
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(2, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals(round($originalState['collection_card_summary']['acquired_price'] * 0.8),
            $secondState['collection_card_summary']['acquired_price']
        );

        // Third State Assertions - new collection card, no new summary
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(1, $thirdState['collection_card_summaries']['total_cards']);
        // $this->assertEquals('MP', $thirdState['collection_card_summary']['condition']);
    }

    public function test_a_collection_card_can_have_variants_on_condition() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => false,
            'tracks_condition' => true,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        // set old condition to the same as new
        // creates a new summary instead of updating one
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'MP',
            'oldCondition'  => 'MP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);

        // Second State Assertions
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(2, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals('LP', $secondState['collection_card_summary']['condition']);

        // Third State Assertions
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(3, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals('MP', $thirdState['collection_card_summary']['condition']);
    }

    public function test_a_collection_card_can_have_variants_on_price() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => true,
            'tracks_condition' => false,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // create new variant with lower price
        // leave the condition the same
        // set old price to the same as new
        //   creates a new summary instead of updating one
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newPrice'      => round($pivot['acquired_price'] * 0.8),
            'oldPrice'      => round($pivot['acquired_price'] * 0.8),
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // create new variant with lower price
        // leave the condition the same
        // set old price to the same as new
        //   creates a new summary instead of updating one
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newPrice'      => round($pivot['acquired_price'] * 0.8),
            'oldPrice'      => round($pivot['acquired_price'] * 0.8),
            'change'        => 1,
        ]);

        // Get the updated state after the price change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);

        // Second State Assertions - new collection card and summary
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(2, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals(
            round($originalState['collection_card_summary']['acquired_price'] * 0.8),
            round($secondState['collection_card_summary']['acquired_price'])
        );

        // Third State Assertions - new collection card, no new summary
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(3, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals(
            round($secondState['collection_card_summary']['acquired_price'] * 0.8),
            round($thirdState['collection_card_summary']['acquired_price'])
        );
    }

    public function test_a_collection_card_can_merge_variants() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $user->settings()->create([
            'tracks_price'     => false,
            'tracks_condition' => true,
        ]);

        // get a new, related card, collection, and folder
        $collectionFolder = $this->createCollectionInFolder();
        $collectionUuid   = $collectionFolder['collection_uuid'];
        $folderUuid       = $collectionFolder['folder_uuid'];
        $cardUuid         = $this->createCollectionCard($collectionUuid);
        $folder           = Folder::uuid($folderUuid);
        $card             = Card::uuid($cardUuid);
        $collection       = Collection::uuid($collectionUuid);

        // Initial state immediately after creation
        // one new card in a collection nested in a folder
        $originalState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        // set old condition to the same as new
        // creates a new summary instead of updating one
        $pivot = $originalState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'LP',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
            'change'        => 1,
        ]);

        // Get the updated state after the condition change
        $secondState = $this->getState($card, $collection, $folder);

        // change the condition to 'LP'
        // leave the price the same
        $pivot = $secondState['pivot'];
        $this->updateCard($pivot, [
            'newCondition'  => 'NM',
            'oldCondition'  => 'LP',
            'newPrice'      => $pivot['acquired_price'],
            'oldPrice'      => $pivot['acquired_price'],
        ]);

        // Get the updated state after the condition change
        $thirdState = $this->getState($card, $collection, $folder);

        // First State Assertions
        $this->assertEquals(1, $originalState['collection_cards']['total_cards']);
        $this->assertEquals(1, $originalState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $originalState['collection_card_summary']['condition']);

        // Second State Assertions
        $this->assertEquals(2, $secondState['collection_cards']['total_cards']);
        $this->assertEquals(2, $secondState['collection_card_summaries']['total_cards']);
        $this->assertEquals('LP', $secondState['collection_card_summary']['condition']);

        // Third State Assertions
        $this->assertEquals(3, $thirdState['collection_cards']['total_cards']);
        $this->assertEquals(1, $thirdState['collection_card_summaries']['total_cards']);
        $this->assertEquals('NM', $thirdState['collection_card_summary']['condition']);
    }
}
