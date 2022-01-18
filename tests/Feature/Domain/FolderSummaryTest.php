<?php

namespace Tests\Feature\Domain;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\MoveCollection;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;

class FolderSummaryTest extends CardCollectionTestCase
{
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

    // folder totals are updated when cards are moved

    // folder totals are updated when folders are moved

    // folder totals are update when cards are deleted

    // folder totals are updated when sub-collections are deleted

    // folder totals are updated when sub-folders are deleted

    // folder totals are updated when card moved to different collection

    // folder totals are updated when card quantities are changed

    // folder totals are updated when card prices are updated

    // folder totals are updated when card acquired prices change
}
