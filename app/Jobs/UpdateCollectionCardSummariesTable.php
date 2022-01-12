<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;

class UpdateCollectionCardSummariesTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private object $summary;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $summary)
    {
        $this->summary = $summary;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $summary = $this->summary;
        $priceWhenAddedValue = Money::of($summary->price_when_added ?: 0, 'USD');
        $priceWhenAddedValueInt = $priceWhenAddedValue->getMinorAmount()->toInt();
        $priceWhenUpdatedValue = Money::of($summary->price_when_updated ?: 0, 'USD');
        $priceWhenUpdatedValueInt = $priceWhenUpdatedValue->getMinorAmount()->toInt();
        $currentPriceValue = Money::of($summary->current_price ?: 0, 'USD');
        $currentPriceValueInt = $currentPriceValue->getMinorAmount()->toInt();

        DB::table('collection_card_summaries')
            ->where('id', '=', $summary->id)
            ->update([
                'price_when_added_int'   => $priceWhenAddedValueInt,
                'price_when_updated_int' => $priceWhenUpdatedValueInt,
                'current_price_int'      => $currentPriceValueInt,
            ]);
    }
}
