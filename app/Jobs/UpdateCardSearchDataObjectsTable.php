<?php

namespace App\Jobs;

use Brick\Money\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateCardSearchDataObjectsTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private object $dataObject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $dataObject)
    {
        $this->dataObject = $dataObject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataObject = $this->dataObject;
        $dataPrices = $dataObject->prices;
        $prices     = [];
        if (strlen($dataPrices) > 0) {
            $prices = json_decode($dataPrices);
        }

        $priceInts = [];
        foreach ($prices as $finish => $price) {
            $priceValue         = Money::of($price ?: 0, 'USD');
            $priceValueInt      = $priceValue->getMinorAmount()->toInt();
            $priceInts[$finish] = $priceValueInt;
        }

        DB::table('card_search_data_objects')
            ->where('id', '=', $dataObject->id)
            ->update([
                'prices' => json_encode($priceInts),
            ]);
    }
}
