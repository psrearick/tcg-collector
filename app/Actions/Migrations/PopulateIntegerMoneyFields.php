<?php

namespace App\Actions\Migrations;

use App\Jobs\UpdateCardCollectionsTable;
use App\Jobs\UpdateCardSearchDataObjectsTable;
use App\Jobs\UpdateCollectionCardSummariesTable;
use App\Jobs\UpdatePricesTable;
use App\Jobs\UpdateSummariesTable;
use Illuminate\Support\Facades\DB;

class PopulateIntegerMoneyFields
{
    public function __invoke()
    {
        // $this->updateSummariesTable();
        // $this->updatePrices();
        // $this->updateCollectionCardSummaries();
        // $this->updateCardCollections();
        $this->updateCardSearchDataObjects();
    }

    private function updateCardCollections() : void
    {
        DB::table('card_collections')
            ->lazyById()->each(function ($cardCollection) {
                UpdateCardCollectionsTable::dispatch($cardCollection);
            });
    }

    private function updateCardSearchDataObjects() : void
    {
        DB::table('card_search_data_objects')
            ->lazyById()->each(function ($dataObject) {
                UpdateCardSearchDataObjectsTable::dispatch($dataObject);
            });
    }

    private function updateCollectionCardSummaries() : void
    {
        DB::table('collection_card_summaries')
            ->lazyById()->each(function ($summary) {
                UpdateCollectionCardSummariesTable::dispatch($summary);
            });
    }

    private function updatePrices() : void
    {
        DB::table('prices')
            ->lazyById()->each(function ($price) {
                UpdatePricesTable::dispatch($price);
            });
    }

    private function updateSummariesTable() : void
    {
        DB::table('summaries')
            ->lazyById()->each(function ($summary) {
                UpdateSummariesTable::dispatch($summary);
            });
    }
}
