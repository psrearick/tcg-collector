<?php

namespace Tests\Feature\Domain;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\createPrice;
use App\Domain\Prices\Aggregate\Actions\MatchFinish;
use App\Domain\Prices\Models\Price;
use App\Jobs\UpdateAncestry;
use App\Jobs\UpdateCollectionTotals;
use App\Jobs\UpdateFolderAncestry;
use App\Jobs\UpdateFolderTotals;
use Brick\Money\Money;

class PriceTest extends CardCollectionTestCase
{
    public function test_a_collection_card_summary_is_updated_when_a_price_is_created() : void
    {
        // set user
        $this->act();

        // create collection card
        $collectionUuid = $this->createCollection();
        $cardUuid       = $this->createCollectionCard($collectionUuid);

        // get state
        $collection     = Collection::uuid($collectionUuid);
        $cardSummary    = $collection->cardSummaries->first();

        // create price data
        $priceData      = Price::factory()->make([
            'card_uuid' => $cardUuid,
            'type'      => (new MatchFinish)($cardSummary->finish),
        ])->toArray();

        // create price
        (new createPrice)($priceData);

        // state
        // stored
        $price = Money::of($priceData['price'], 'USD')
            ->getMinorAmount()->toInt();

        // previous
        $acquired   = $cardSummary->price_when_added;
        $current    = $cardSummary->current_price;

        // updated
        $cardSummary->refresh();
        $acquired2  = $cardSummary->price_when_added;
        $current2   = $cardSummary->current_price;

        // assertions
        $this->assertEquals($acquired, $acquired2);
        $this->assertEquals($acquired, $current);
        $this->assertEquals($price, $current2);
        $this->assertNotEquals($price, $acquired);
    }

    public function test_a_collection_summary_is_updated_when_a_price_is_created() : void
    {
        // set user
        $this->act();

        // create collection card
        $collectionUuid = $this->createCollection();
        $cardUuid       = $this->createCollectionCard($collectionUuid);

        // get models
        $card           = Card::uuid($cardUuid);
        $collection     = Collection::uuid($collectionUuid);
        assert($collection instanceof Collection);

        // get state
        $state = $this->getState($card, $collection);

        // create price data
        $priceData      = Price::factory()->make([
            'card_uuid' => $cardUuid,
            'type'      => (new MatchFinish)($state['collection_card_summary']['finish']),
        ])->toArray();

        // // create price
        (new createPrice)($priceData);

        $price = Money::of($priceData['price'], 'USD')
            ->getMinorAmount()->toInt();

        $this->simulatePriceUpdateSummaryJob();

        // get state
        $state2 = $this->getState($card, $collection);

        // assertions
        $this->assertEquals($price, $state2['collection']['current_value']);
        $this->assertNotEquals($price, $state2['collection']['acquired_value']);
        $this->assertNotEquals($price, $state['collection']['acquired_value']);
        $this->assertNotEquals($price, $state['collection']['acquired_value']);
    }

    public function test_a_parent_folder_summary_is_updated_when_a_child_price_is_updated() : void
    {
        // set user
        $this->act();

        // create collection card
        $folderCollection   = $this->createCollectionInFolder();
        $folderUuid         = $folderCollection['folder_uuid'];
        $collectionUuid     = $folderCollection['collection_uuid'];
        $cardUuid           = $this->createCollectionCard($collectionUuid);

        // get models
        $card           = Card::uuid($cardUuid);
        $collection     = Collection::uuid($collectionUuid);
        $folder         = Folder::uuid($folderUuid);

        // get state
        $state = $this->getState($card, $collection, $folder);

        // create price data
        $priceData      = Price::factory()->make([
            'card_uuid' => $cardUuid,
            'type'      => (new MatchFinish)($state['collection_card_summary']['finish']),
        ])->toArray();

        // // create price
        (new createPrice)($priceData);

        $this->simulatePriceUpdateSummaryJob();

        $price = Money::of($priceData['price'], 'USD')
            ->getMinorAmount()->toInt();

        // get state
        assert($folder instanceof Folder);
        $state2 = $this->getState($card, $collection, $folder);

        // assertions
        $this->assertEquals($price, $state2['folder']['current_value']);
        $this->assertNotEquals($price, $state2['folder']['acquired_value']);
        $this->assertNotEquals($price, $state['folder']['acquired_value']);
        $this->assertNotEquals($price, $state['folder']['acquired_value']);
    }

    public function test_a_price_can_be_created() : void
    {
        // set user
        $this->act();

        // get card
        $card       = Card::take(1)->first();

        // get card prices
        $prices     = $card->prices;

        // get price data
        $priceData  = Price::factory()->make([
            'card_uuid' => $card->uuid,
        ])->toArray();
        $type       = $priceData['type'];

        // create price
        (new createPrice)($priceData);

        $price      = Money::of($priceData['price'], 'USD')
            ->getMinorAmount()->toInt();

        // refresh card data
        $card->refresh();

        // get prices for comparisons
        $initialPrices      = $prices->where('type', '=', $type);
        $initialCount       = $initialPrices->count();
        $initialPrice       = $initialCount > 0
            ? $initialPrice = $initialPrices->last()->price
            : 0;

        $newPrices      = $card->prices->where('type', '=', $type);
        $newCount       = $newPrices->count();
        $newPrice       = optional($newPrices->last())->price;

        // assertions
        $this->assertNotNull($newPrice);
        $this->assertNotEquals($initialPrice, $newPrice);
        $this->assertGreaterThan($initialCount, $newCount);
        $this->assertEquals($price, $newPrice);
    }

    private function simulateCollectionUpdateJob() : void
    {
        // call command summaries:update
        $collections = Collection::withoutGlobalScopes([UserScope::class, UserScopeNotShared::class])
            ->whereNull('deleted_at')
            ->get();

        $collections->each(function (Collection $collection) {
            (new UpdateCollectionTotals($collection))->handle();
        });
    }

    /** @see UpdateFolderAncestry */
    private function simulateFolderUpdateJob() : void
    {
        Folder::withDepth()
            ->groupBy('id')
            ->having('depth', '=', 0)
            ->get()
            ->each(fn ($folder) => $this->updateFolderDescendants($folder));
    }

    /**
     * @see UpdateAllSummaries
     * @see UpdateAncestry
     */
    private function simulatePriceUpdateSummaryJob() : void
    {
        $this->simulateCollectionUpdateJob();
        $this->simulateFolderUpdateJob();
    }

    private function updateFolderDescendants(Folder $folder) : void
    {
        $folder->children->each(function ($child) {
            $this->updateFolderDescendants($child);
        });

        (new UpdateFolderTotals($folder))->handle();
    }
}
