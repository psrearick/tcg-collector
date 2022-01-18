<?php

namespace Tests\Feature\Domain;

use App\Domain\Collections\Aggregate\Actions\MoveCollection;
use App\Domain\Collections\Aggregate\Actions\MoveCollectionCards;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Domain\Folders\Models\Folder;

class FolderSummaryTest extends CardCollectionTestCase
{
    public function test_a_folder_totals_are_updated_when_cards_are_moved() : void
    {
        // set user
        $user = $this->act();

        // create folder
        // create collection in folder
        $folderCollection   = $this->createCollectionInFolder('First Folder');
        $collectionUuid     = $folderCollection['collection_uuid'];
        $folderUuid         = $folderCollection['folder_uuid'];

        // create cards in collection
        $col1Card1  = $this->createCollectionCard($collectionUuid, 0, '', 2);
        $col1Card2  = $this->createCollectionCard($collectionUuid, 1, '', 3);
        $col1Card3  = $this->createCollectionCard($collectionUuid, 2, '', 5);

        // Get state
        $collection = Collection::uuid($collectionUuid);
        $folder     = Folder::uuid($folderUuid);
        $firstState = $this->getState(null, $collection, $folder);
        $totals     = $firstState['total_collection_cards'];

        // ASSERT: Cards are in collection
        $this->assertEquals(10, $totals['quantity']);
        $this->assertEquals(3, $totals['cards']);

        // ASSERT: Collection summary is accurate
        $this->assertEquals($totals['quantity'], $firstState['collection']['total_cards']);
        $this->assertEquals($totals['price'], $firstState['collection']['current_value']);

        // ASSERT: Folder summary is accurate
        $this->assertEquals($totals['quantity'], $firstState['folder']['total_cards']);
        $this->assertEquals($totals['price'], $firstState['folder']['current_value']);

        // create another folder
        // create another collection in the folder
        $folderCollection2  = $this->createCollectionInFolder('First Folder');
        $collection2Uuid    = $folderCollection2['collection_uuid'];
        $folder2Uuid        = $folderCollection2['folder_uuid'];

        // create cards in the new collection
        $col2Card1  = $this->createCollectionCard($collection2Uuid, 4, '', 1);
        $col2Card2  = $this->createCollectionCard($collection2Uuid, 5, '', 4);
        $col2Card3  = $this->createCollectionCard($collection2Uuid, 6, '', 7);

        // Get state
        $collection2    = Collection::uuid($collection2Uuid);
        $folder2        = Folder::uuid($folder2Uuid);
        $firstState2    = $this->getState(null, $collection2, $folder2);
        $totals2        = $firstState2['total_collection_cards'];

        // ASSERT: Cards are in new collection
        $this->assertEquals(12, $totals2['quantity']);
        $this->assertEquals(3, $totals2['cards']);

        // ASSERT: New collection summary is accurate
        $this->assertEquals($totals2['quantity'], $firstState2['collection']['total_cards']);
        $this->assertEquals($totals2['price'], $firstState2['collection']['current_value']);

        // ASSERT: New folder summary is accurate
        $this->assertEquals($totals2['quantity'], $firstState2['folder']['total_cards']);
        $this->assertEquals($totals2['price'], $firstState2['folder']['current_value']);

        // move cards from the first collection to the second
        $cardsToMove = $collection->cardSummaries()
            ->whereIn('card_uuid', [$col1Card1, $col1Card2])
            ->get()
            ->toArray();

        (new MoveCollectionCards)($collectionUuid, $collection2Uuid, $cardsToMove);

        // get state
        $secondState    = $this->getState(null, $collection, $folder);
        $secondTotals   = $secondState['total_collection_cards'];
        $secondState2   = $this->getState(null, $collection2, $folder2);
        $secondTotals2  = $secondState2['total_collection_cards'];

        // ASSERT: first collection has fewer cards
        $this->assertEquals(5, $secondTotals['quantity']); // quantity
        $this->assertEquals(5, $secondTotals['cards']); // transactions

        // ASSERT: first collection summary is accurate
        $this->assertEquals($secondTotals['quantity'], $secondState['collection']['total_cards']);
        $this->assertEquals($secondTotals['price'], $secondState['collection']['current_value']);
        // ASSERT: first folder summary is accurate
        $this->assertEquals($secondTotals['quantity'], $secondState['folder']['total_cards']);
        $this->assertEquals($secondTotals['price'], $secondState['folder']['current_value']);
        // ASSERT: second collection has more cards
        $this->assertEquals(17, $secondTotals2['quantity']); // quantity
        $this->assertEquals(5, $secondTotals2['cards']); // transactions
        // ASSERT: second collection summary is accurate
        $this->assertEquals($secondTotals2['quantity'], $secondState2['collection']['total_cards']);
        $this->assertEquals($secondTotals2['price'], $secondState2['collection']['current_value']);
        // ASSERT: second folder summary is accurate
        $this->assertEquals($secondTotals2['quantity'], $secondState2['folder']['total_cards']);
        $this->assertEquals($secondTotals2['price'], $secondState2['folder']['current_value']);

        // move cards from the second collection to the first
        $cardsToMove2 = $collection2->cardSummaries()
            ->whereIn('card_uuid', [$col2Card1, $col2Card2])
            ->get()
            ->toArray();

        (new MoveCollectionCards)($collection2Uuid, $collectionUuid, $cardsToMove2);

        // get state
        $thirdState    = $this->getState(null, $collection, $folder);
        $thirdTotals   = $thirdState['total_collection_cards'];
        $thirdState2   = $this->getState(null, $collection2, $folder2);
        $thirdTotals2  = $thirdState2['total_collection_cards'];

        // ASSERT: first collection has more cards
        $this->assertEquals(10, $thirdTotals['quantity']); // quantity
        $this->assertEquals(7, $thirdTotals['cards']); // transactions
        // ASSERT: first collection summary is accurate
        $this->assertEquals($thirdTotals['quantity'], $thirdState['collection']['total_cards']);
        $this->assertEquals($thirdTotals['price'], $thirdState['collection']['current_value']);
        // ASSERT: first folder summary is accurate
        $this->assertEquals($thirdTotals['quantity'], $thirdState['folder']['total_cards']);
        $this->assertEquals($thirdTotals['price'], $thirdState['folder']['current_value']);
        // ASSERT: second collection has fewer cards
        $this->assertEquals(12, $thirdTotals2['quantity']); // quantity
        $this->assertEquals(7, $thirdTotals2['cards']); // transactions
        // ASSERT: second collection summary is accurate
        $this->assertEquals($thirdTotals2['quantity'], $thirdState2['collection']['total_cards']);
        $this->assertEquals($thirdTotals2['price'], $thirdState2['collection']['current_value']);
        // ASSERT: second folder summary is accurate
        $this->assertEquals($thirdTotals2['quantity'], $thirdState2['folder']['total_cards']);
        $this->assertEquals($thirdTotals2['price'], $thirdState2['folder']['current_value']);
    }

    public function test_folder_summaries_are_updated_when_a_card_is_added() : void
    {
        // set user
        $this->act();

        // create collection in folder
        $folderCollection   = $this->createCollectionInFolder();
        $collectionUuid     = $folderCollection['collection_uuid'];
        $folderUuid         = $folderCollection['folder_uuid'];

        // add a single card to collection
        $this->createCollectionCard($collectionUuid);

        // get collection card
        $collection         = Collection::uuid($collectionUuid);
        $collectionCard     = $collection->cards->first()->pivot;
        $cardPrice          = $collectionCard->price_when_added;
        $totalPrice         = $cardPrice;

        // get folder summary
        $folder             = Folder::uuid($folderUuid);
        $summary            = $folder->summary;

        // ASSERT: folder total is updated
        $this->assertNotNull($summary);
        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(1, $summary->total_cards);

        // add another card to the collection, quantity of 3
        $this->createCollectionCard($collectionUuid, 1, '', 3);

        // refresh the folder and collection instances
        $collection->refresh();
        $folder->refresh();

        // get collection card
        $collectionCard     = $collection->cards->last()->pivot;
        $cardPrice          = $collectionCard->price_when_added;
        $totalPrice         = $totalPrice + ($cardPrice * 3);

        // get summary
        $summary            = $folder->summary;

        // ASSERT: folder total is updated again
        $this->assertNotNull($summary);
        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(4, $summary->total_cards);
    }

    public function test_folder_totals_are_updated_when_collections_are_moved() : void
    {
        // set user
        $user = $this->act();

        // create collection in folder 01
        $folderCollection   = $this->createCollectionInFolder();
        $collectionUuid     = $folderCollection['collection_uuid'];
        $folderUuid         = $folderCollection['folder_uuid'];

        // create folder 02
        $folder2Uuid        = $this->createFolder('folder 02');

        // add cards to collection
        $this->createCollectionCard($collectionUuid);

        // get collection card
        $collection         = Collection::uuid($collectionUuid);
        $collectionCard     = $collection->cards->first()->pivot;
        $cardPrice          = $collectionCard->price_when_added;
        $totalPrice         = $cardPrice;

        // get folder summary
        $folder             = Folder::uuid($folderUuid);
        $summary            = $folder->summary;

        // ASSERT: folder 01 totals reflect cards added
        $this->assertNotNull($summary);
        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(1, $summary->total_cards);

        // move collection to folder 02
        (new MoveCollection)($collectionUuid, $folder2Uuid, $user->id);

        // ASSERT: folder 01 total is 0
        $folder->refresh();
        $summary = $folder->summary;
        $this->assertNotNull($summary);
        $this->assertEquals(0, $summary->current_value);
        $this->assertEquals(0, $summary->total_cards);

        // ASSERT: folder 02 total is up
        $folder2 = Folder::uuid($folder2Uuid);
        $summary = $folder2->summary;
        $this->assertNotNull($summary);
        $this->assertEquals($totalPrice, $summary->current_value);
        $this->assertEquals(1, $summary->total_cards);
    }

    // folder totals are updated when folders are moved
    public function test_folder_totals_are_updated_when_folders_are_moved() : void
    {
        // set user
        $user = $this->act();
        $uid  = $user->id;

        //########## Folders ##########\\
        // create parent folders
        $p1 = $this->createFolder('p1');
        $p2 = $this->createFolder('p2');

        // create p1 children
        $p1f1 = $this->createFolder('p1f1', $p1);
        $p1f2 = $this->createFolder('p1f2', $p1);

        // create p2 children
        $p2f1 = $this->createFolder('p2f1', $p2);
        $p2f2 = $this->createFolder('p2f2', $p2);

        //########## Collections ##########\\
        // create p1 collections
        $p1c1   = $this->createCollection($p1);
        $p1f1c1 = $this->createCollection($p1f1);
        $p1f1c2 = $this->createCollection($p1f1);
        $p1f2c1 = $this->createCollection($p1f2);
        $p1f2c2 = $this->createCollection($p1f2);

        // create p2 collections
        $p2c1   = $this->createCollection($p2);
        $p2f1c1 = $this->createCollection($p2f1);
        $p2f1c2 = $this->createCollection($p2f1);
        $p2f2c1 = $this->createCollection($p2f2);
        $p2f2c2 = $this->createCollection($p2f2);

        //########## Cards ##########\\
        // create p1 cards
        $p1c1a1    = $this->createCollectionCard($p1c1, 0, '', 2);
        $p1f1c1a1  = $this->createCollectionCard($p1f1c1, 1, '', 2);
        $p1f1c2a1  = $this->createCollectionCard($p1f1c2, 2, '', 2);
        $p1f2c1a1  = $this->createCollectionCard($p1f2c1, 3, '', 2);
        $p1f2c2a1  = $this->createCollectionCard($p1f2c2, 4, '', 2);

        // create p2 cards
        $p2c1a1    = $this->createCollectionCard($p2c1, 5, '', 2);
        $p2f1c1a1  = $this->createCollectionCard($p2f1c1, 6, '', 2);
        $p2f1c2a1  = $this->createCollectionCard($p2f1c2, 7, '', 2);
        $p2f2c1a1  = $this->createCollectionCard($p2f2c1, 8, '', 2);
        $p2f2c2a1  = $this->createCollectionCard($p2f2c2, 9, '', 2);

        // Folders-Collections
        // p1 -> p1c1
        // p1 -> p1f1 -> p1f1c1, p1f1c2
        // p1 -> p1f2 -> p1f1c1, p1f2c2

        // p2 -> p2c1
        // p2 -> p2f1 -> p2f1c1, p2f1c1
        // p2 -> p2f2 -> p2f2c1, p2f2c2

        //########## Models ##########\\
        // get parent models
        $p1Mod = Folder::uuid($p1);
        $p2Mod = Folder::uuid($p2);

        //########## Initial State ##########\\
        // parent checks
        $p1st1 = $this->getFolderSummary($p1Mod);
        $p2st1 = $this->getFolderSummary($p2Mod);

        // assertions
        $this->assertEquals(10, $p1st1['total_cards']);
        $this->assertEquals(10, $p2st1['total_cards']);

        //########## Move Folders - P1 -> P2 ##########\\
        (new MoveFolder)($p1f1, $p2, $uid);

        // Folders-Collections
        // p1 -> p1c1
        // p1 -> p1f2 -> p1f1c1, p1f2c2

        // p2 -> p2c1
        // p2 -> p1f1 -> p1f1c1, p1f1c2
        // p2 -> p2f1 -> p2f1c1, p2f1c1
        // p2 -> p2f2 -> p2f2c1, p2f2c2

        // get state
        $p1st2 = $this->getFolderSummary($p1Mod);
        $p2st2 = $this->getFolderSummary($p2Mod);

        // calculations
        // p1 loss, p2 gain
        $p1loss = $p1st1['current_value'] - $p1st2['current_value'];
        $p2gain = $p2st2['current_value'] - $p2st1['current_value'];

        // assertions
        $this->assertEquals($p1loss, $p2gain);
        $this->assertEquals(6, $p1st2['total_cards']);
        $this->assertEquals(14, $p2st2['total_cards']);

        //########## Move Folders - P2 -> P1 ##########\\
        (new MoveFolder)($p1f1, $p1, $uid);
        (new MoveFolder)($p2f2, $p1, $uid);

        // Folders-Collections
        // p1 -> p1c1
        // p2 -> p1f1 -> p1f1c1, p1f1c2
        // p1 -> p1f2 -> p1f1c1, p1f2c2
        // p2 -> p2f2 -> p2f2c1, p2f2c2

        // p2 -> p2c1
        // p2 -> p2f1 -> p2f1c1, p2f1c1

        // get state
        $p1st3 = $this->getFolderSummary($p1Mod);
        $p2st3 = $this->getFolderSummary($p2Mod);

        // calculations
        // p1 gain, p2 loss
        $p1gain = $p1st3['current_value'] - $p1st2['current_value'];
        $p2loss = $p2st2['current_value'] - $p2st3['current_value'];

        // assertions
        $this->assertEquals($p2loss, $p1gain);
        $this->assertEquals(14, $p1st3['total_cards']);
        $this->assertEquals(6, $p2st3['total_cards']);
    }

    public function test_folders_are_update_when_cards_are_deleted()
    {
        // set user
        $user = $this->act();

        // create collection in folder 01
        $folderCollection   = $this->createCollectionInFolder();
        $collectionUuid     = $folderCollection['collection_uuid'];
        $folderUuid         = $folderCollection['folder_uuid'];

        // add cards to collection
        $c1 = $this->createCollectionCard($collectionUuid, 1);
        $c2 = $this->createCollectionCard($collectionUuid, 2);
        $c3 = $this->createCollectionCard($collectionUuid, 3);

        // get models
        $folder     = Folder::uuid($folderUuid);
        $collection = Collection::uuid($collectionUuid);

        // get state
        $state1 = $this->getFolderSummary($folder);

        // assert quantity
        $this->assertEquals(3, $state1['total_cards']);

        // delete cards
        $cardsToDelete = $collection->cardSummaries()
            ->whereIn('card_uuid', [$c2, $c3])
            ->get()
            ->toArray();

        $this->deleteCards($collectionUuid, $cardsToDelete);

        // get state
        $state2 = $this->getFolderSummary($folder);

        // assert quantity changed
        $this->assertEquals(1, $state2['total_cards']);
    }

    // folder totals are updated when sub-collections are deleted

    // folder totals are updated when sub-folders are deleted

    // folder totals are updated when card moved to different collection

    // folder totals are updated when card quantities are changed

    // folder totals are updated when card prices are updated

    // folder totals are updated when card acquired prices change
}
