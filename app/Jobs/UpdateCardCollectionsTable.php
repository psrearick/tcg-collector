<?php

namespace App\Jobs;

use Brick\Money\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateCardCollectionsTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private object $cardCollection;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $cardCollection)
    {
        $this->cardCollection = $cardCollection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cardCollection         = $this->cardCollection;
        $priceWhenAddedValue    = Money::of($cardCollection->price_when_added ?: 0, 'USD');
        $priceWhenAddedValueInt = $priceWhenAddedValue->getMinorAmount()->toInt();

        DB::table('card_collections')
            ->where('id', '=', $cardCollection->id)
            ->update([
                'price_when_added_int' => $priceWhenAddedValueInt,
            ]);
    }
}
